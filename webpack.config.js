const path = require('path')
const TerserPlugin = require('terser-webpack-plugin')

module.exports = {
   entry: {
      publish: './cavwp/wp-content/themes/writers-camp/assets_dev/publish.ts',
      all: './cavwp/wp-content/themes/writers-camp/assets_dev/all.ts',
   },
   module: {
      rules: [
         {
            test: /\.tsx?$/,
            use: 'ts-loader',
            exclude: /node_modules/,
         },
      ],
   },
   resolve: {
      extensions: ['.tsx', '.ts', '.js'],
   },
   output: {
      filename: '[name].min.js',
      path: path.resolve(
         __dirname,
         'cavwp',
         'wp-content',
         'themes',
         'writers-camp',
         'assets'
      ),
   },
   optimization: {
      minimize: true,
      minimizer: [new TerserPlugin()],
   },
}
