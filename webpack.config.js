const webpack = require('webpack');
const glob = require("glob");
const path = require("path");
const fs = require("fs");
const autoprefixer = require('autoprefixer');
const prompt = require('inquirer').createPromptModule();

/*
 * SplitChunksPlugin is enabled by default and replaced
 * deprecated CommonsChunkPlugin. It automatically identifies modules which
 * should be splitted of chunk by heuristics using module duplication count and
 * module category (i. e. node_modules). And splits the chunks…
 *
 * It is safe to remove "splitChunks" from the generated configuration
 * and was added as an educational example.
 *
 * https://webpack.js.org/plugins/split-chunks-plugin/
 *
 */

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const OptimizeCssnanoPlugin = require('@intervolga/optimize-cssnano-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const FontminPlugin = require('./public/assets/webpack/fontmin-webpack.js');

/*
 * We've enabled HtmlWebpackPlugin for you! This generates a html
 * page for you when you compile webpack, which will make you start
 * developing and prototyping faster.
 *
 * https://github.com/jantimon/html-webpack-plugin
 *
 */

// const getFontmin = () => {
// 	let charList = glob.sync(path.join(__dirname, "/views/*")).map((file) => {
// 		return fs.readFileSync(file)
// 	})
//
// 	return new FontminPlugin({
// 		autodetect: true, // automatically pull unicode characters from CSS
// 		glyphs: Buffer.concat(charList).toString('utf-8').split(""),
// 	})
// };


function GetPlugins() {
    let list = [
        new webpack.ProgressPlugin(),
        new MiniCssExtractPlugin({
            // Options similar to the same options in webpackOptions.output
            // all options are optional
            filename: process.env.NODE_ENV === 'development' ? "css/[name].css" : "css/[name].min.css",
            ignoreOrder: false, // Enable to remove warnings about conflicting order
        }),

        new CleanWebpackPlugin(),

        new VueLoaderPlugin(),
    ];

    if (process.env.NODE_ENV !== 'development') {
        list.push(new OptimizeCssnanoPlugin({
            sourceMap: true,
            cssnanoOptions: {
                preset: [
                    'default',
                    {
                        mergeLonghand: false,
                        cssDeclarationSorter: false
                    }
                ]
            }
        }));
    }

    return list;
}

let config = {
    mode: 'development',
    entry: [
        './public/assets/default/main.js',
        './public/assets/static/style/main.scss'
    ],

    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'js/[name].js',
        chunkFilename: "js/[name].js",
    },

    resolve: {
        alias: {
            'vue$': (process.env.NODE_ENV === 'development' ?
                "vue/dist/vue.esm.js" :
                "vue/dist/vue.min.js"),
            "jquery.inputmask$": "jquery.inputmask/dist/jquery.inputmask.bundle.js",
        }
    },

    plugins: GetPlugins(),

    module: {
        rules: [
            {
                test: require.resolve('jquery'),
                use:[
                    {
                        loader: 'expose-loader',
                        options: {
                            exposes: ['$', 'jQuery'],
                        },
                    }
                ]
            },
            {
                test: /.(js)$/,
                include: [
                    path.resolve(__dirname, 'assets'),
                ],
                exclude: /(node_modules|webpack)/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: {
                            plugins: [
                                [
                                    "@babel/plugin-transform-template-literals", {
                                    loose: true
                                }],
                                "@babel/plugin-transform-runtime",
                                "@babel/plugin-syntax-dynamic-import"
                            ],

                            presets: [
                                [
                                    '@babel/preset-env',
                                    {
                                        modules: false,
                                        useBuiltIns: "usage",
                                        corejs: 3
                                    }
                                ]
                            ]
                        }
                    },
                ],

            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {
                            // you can specify a publicPath here
                            // by default it uses publicPath in webpackOptions.output
                            publicPath: "../",
                            hmr: process.env.NODE_ENV === 'development',
                        },
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: true
                        }
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            sourceMap: true,
                            plugins: () => [autoprefixer()],
                        }
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            // prependData: '@import "common";',
                            sourceMap: true,
                            sassOptions: {
                                includePaths: [
                                    path.resolve(__dirname, "./assets/static/style/"),
                                    path.resolve(__dirname, "node_modules")
                                ]
                            }
                        }
                    },
                ],
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2|png|jpe?g|gif)$/i,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[path][name].[ext]'
                        },
                    },
                ],
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.css$/,
                use: [
                    'vue-style-loader',
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {
                            // you can specify a publicPath here
                            // by default it uses publicPath in webpackOptions.output
                            publicPath: "../",
                            hmr: process.env.NODE_ENV === 'development',
                        },
                    },
                    'css-loader'
                ]
            },
        ]
    },

    optimization: {
        splitChunks: {
            cacheGroups: {
                vendor:{
                    name: "vendor",
                    chunks: 'initial',
                    test: (module, chunk) => {
                        const npmRule = /(node_modules)/i,
                            trumbowygRule = /(trumbowyg)/i,
                            cssRule = /\.(css|s[ac]ss)$/i,
                            resourcePath = module.resource;

                        if (npmRule.test(resourcePath) &&
                            !cssRule.test(resourcePath) &&
                            !trumbowygRule.test(resourcePath)) {

                            return true;
                        }

                        return false;
                    },
                    priority: 5,
                },
            },

            chunks: 'async',
            minChunks: 1,
            minSize: 30000,
            name: false
        },

        minimize: process.env.NODE_ENV !== 'development',
    },

    devtool: "source-map",
    watchOptions: {
        ignored: /node_modules/
    }
};

module.exports = prompt([
    {
        type: 'list',
        name: 'namespace',
        message: '请选择编译目录',
        choices: glob.sync(path.join(__dirname, "public/assets/*/")).map(function (p) {
            return path.basename(p);
        })
    }
]).then(function (result) {

    if (result.namespace == "admin") {
        config.entry = [
            "./public/assets/admin/main.js",
            "bootstrap/dist/css/bootstrap.min.css",
            "metismenu/dist/metisMenu.min.css",
            "font-awesome/css/font-awesome.min.css",
            "./public/assets/common/common-css/customize.scss",
        ];
        config.output.path = path.join(__dirname, "public/static/admin/dev");
        config.output.publicPath = "/static/admin/dev/";

        if (process.env.NODE_ENV !== 'development') {
            config.output.filename = "js/[name].min.js";
            config.output.chunkFilename = "js/[name].min.js";
            config.output.path = path.join(__dirname, "public/static/admin/dist");
            config.output.publicPath = "/static/admin/dist/";
        }
    }

    return Promise.resolve(config);
});
