const path = require('path')
const webpack = require('webpack')
const fs = require('fs');


module.exports = {
  entry: './src/main.js',
  output: {
    path: path.resolve(__dirname, './dist'),
    publicPath: '/dist/',
    filename: 'build.js'
  },
  module: {
    rules: [
      {
        test: /\.less$|\.css$/,
        use: [
          'style-loader',
          { loader: 'css-loader', options: { importLoaders: 1 } },
          'less-loader'
        ]
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loaders: {
            // Since sass-loader (weirdly) has SCSS as its default parse mode, we map
            // the "scss" and "sass" values for the lang attribute to the right configs here.
            // other preprocessors should work out of the box, no loader config like this nessessary.
            'scss': 'vue-style-loader!css-loader!sass-loader',
            'sass': 'vue-style-loader!css-loader!sass-loader?indentedSyntax',
          }
          // other vue-loader options go here
        }
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: /node_modules/
      },
      {
        test: /\.(woff|woff2|eot|ttf|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
        loader: 'url-loader',
        /*
        options: {
          name: '[name].[ext]?[hash]'
        }
        */
      }
    ]
  },
  resolve: {
    alias: {
      'vue$': 'vue/dist/vue.common.js'
    }
  },
  devServer: {
    historyApiFallback: true,
    noInfo: true,
    host: "fedpival.indiza.com", // Your Computer Name
    port: 8181,
    compress: true
  },
  performance: {
    hints: false
  },
  devtool: '#eval-source-map'
}

if (process.env.NODE_ENV === 'production') {
  module.exports.devtool = '#source-map'
  // http://vue-loader.vuejs.org/en/workflow/production.html
  module.exports.plugins = (module.exports.plugins || []).concat([
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: '"production"'
      }
    }),
    new webpack.optimize.UglifyJsPlugin({
      sourceMap: false,
      compress: {
        warnings: false
      }
    }),
    new webpack.LoaderOptionsPlugin({
      minimize: true
    }),
	function () {
	    this.plugin("done", function (stats) {
	    	console.log('build hash replace');
	        var replaceInFile = function (filePath, toReplace, replacement) {
	            var replacer = function (match) {
	                console.log('Replacing in %s: %s => %s', filePath, match, replacement);
	                return replacement
	            };
	            var str = fs.readFileSync(filePath, 'utf8');
	            var out = str.replace(new RegExp(toReplace, 'g'), replacer);
	            fs.writeFileSync(filePath, out);
	        };
	
	        var hash = stats.hash; // Build's hash, found in `stats` since build lifecycle is done.
		
	        replaceInFile(path.join(path.resolve(__dirname, '.'), 'index.html'),
	            'build\.js\?.*"',
	            'build.js?' + hash + '"'
	        );
	    });
	}
  ])
}



plugins: [
	new webpack.ProvidePlugin({
		mapboxgl: 'mapbox-gl'
	})
]
