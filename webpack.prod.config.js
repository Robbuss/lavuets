let webpack = require("webpack");
const {
    VueLoaderPlugin
} = require("vue-loader");
const VuetifyLoaderPlugin = require("vuetify-loader/lib/plugin")

let path = require("path");
const utils = {
    resolve: function(dir) {
        return path.join(__dirname, dir)
    },

    assetsPath: function(_path) {
        const assetsSubDirectory = "static"
        return path.posix.join(assetsSubDirectory, _path)
    }
}
module.exports = {
    entry: {
        app: "./resources/js/app.ts"
    },
    resolve: {
        extensions: [".js", ".vue", ".json", ".ts"],
        alias: {
            js: path.resolve(__dirname, "resources/js"),
            // "components": utils.resolve("src/components"),
            "vue$": "vue/dist/vue.esm.js"
        }
    },
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                exclude: [/node_modules/],
                use: {
                    loader: "ts-loader",
                    options: {
                        appendTsSuffixTo: [/\.vue$/]
                    }
                }
            },
            {
                test: /\.vue$/,
                use: {
                    loader: "vue-loader"
                }
            },
            {
                test: /\.css$/,
                use: ["vue-style-loader", "css-loader"]
            },
            {
                test: /\.styl(us)?$/,
                use: ["vue-style-loader", "css-loader", "stylus-loader"]
            },
            {
                test: /\.(scss)|(sass)$/,
                use: [
                    "vue-style-loader",
                    //"style-loader", // creates style nodes from JS strings
                    "css-loader", // translates CSS into CommonJS
                    "sass-loader" // compiles Sass to CSS, using Node Sass by default
                ],
                enforce: "pre"
            },
            {
                test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
                use: {
                    loader: "url-loader",
                    options: {
                        limit: 10000,
                        name: utils.assetsPath("img/[name].[hash:7].[ext]")
                    }
                }
            },
            {
                test: /\.(mp4|webm|ogg|mp3|wav|flac|aac)(\?.*)?$/,
                use: {
                    loader: "url-loader",
                    options: {
                        limit: 10000,
                        name: utils.assetsPath("media/[name].[hash:7].[ext]")
                    }
                }
            },
            {
                test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
                use: {
                    loader: "url-loader",
                    options: {
                        limit: 10000,
                        name: utils.assetsPath("fonts/[name].[hash:7].[ext]")
                    }
                }
            }
        ]
    },
    output: {
        filename: "js/app.js",
        path: utils.resolve("public"),
        pathinfo: false,
        publicPath: "/"
            // chunkFilename: "js/[name]-[hash].js", // for long term caching
    },
    plugins: [
        new VueLoaderPlugin(),
        new VuetifyLoaderPlugin(),
    ]
}
