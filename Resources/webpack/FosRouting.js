/**
 * @author Adrien Foulon <tofandel@tukan.hu>
 */
const fs = require('fs');
const path = require('path');
const util = require('util');

const InjectPlugin = require('webpack-inject-plugin').default;

const execFile = util.promisify(require('child_process').execFile);
const readFile = util.promisify(fs.readFile);
const rmFile = util.promisify(fs.rm);
const writeFile = util.promisify(fs.writeFile);
const makeDir = util.promisify(fs.mkdir)

class FosRouting {
    default = {
        locale: '',
        prettyPrint: false,
        domain: [],
    };

    constructor(options = {}) {
        this.options = Object.assign({target: 'var/cache/fosRoutes.json'}, this.default, options, {format: 'json'});
        this.finalTarget = path.resolve(process.cwd(), this.options.target);
        this.options.target = path.resolve(process.cwd(), this.options.target.replace(/\.json$/, '.tmp.json'));

        if (this.options.target === this.finalTarget) {
            this.options.target += '.tmp';
        }
    }

    // Values don't need to be escaped because node already does that
    shellArg(key, value) {
        key = this.kebabize(key);
        return typeof value === 'boolean' ? (value ? '--' + key : '') : '--' + key + '=' + value;
    }

    kebabize(str) {
        return str.split('').map((letter, idx) => {
            return letter.toUpperCase() === letter
                ? `${idx !== 0 ? '-' : ''}${letter.toLowerCase()}`
                : letter;
        }).join('');
    }

    apply(compiler) {
        let prevContent = null;
        try {
            fs.readFileSync(this.finalTarget);
        } catch (e) {
        }
        const compile = async (comp, callback) => {
            const args = Object.keys(this.options).reduce((pass, key) => {
                const val = this.options[key];
                if (val !== this.default[key]) {
                    if (Array.isArray(val)) {
                        pass.push(...val.map((v) => this.shellArg(key, v)));
                    } else {
                        pass.push(this.shellArg(key, val));
                    }
                }
                return pass;
            }, []);
            await execFile('bin/console', ['fos:js-routing:dump', ...args]);
            const content = await readFile(this.options.target);
            await rmFile(this.options.target);
            if (!prevContent || content.compare(prevContent) !== 0) {
                await makeDir(path.dirname(this.finalTarget), {recursive: true});
                await writeFile(this.finalTarget, content);
                prevContent = content;
                if (comp.modifiedFiles && !comp.modifiedFiles.has(this.finalTarget)) {
                    comp.modifiedFiles.add(this.finalTarget);
                }
            }
            callback();
        };
        compiler.hooks.beforeRun.tapAsync('RouteDump', compile);
        compiler.hooks.watchRun.tapAsync('RouteDump_Watch', (comp, callback) => {
            if (!comp.modifiedFiles || !comp.modifiedFiles.has(this.finalTarget)) {
                compile(comp, callback);
            } else {
                callback();
            }
        });

        new InjectPlugin(() => {
            return 'import Routing from "fos-router";' +
                'import routes from "' + this.finalTarget + '";' +
                'Routing.setRoutingData(routes);';
        }).apply(compiler);
    }
}

module.exports = FosRouting;
module.exports.default = FosRouting;
