export default {
	title: {
	  inner: 'Federació de Pilota Valenciana'
	},
	// Meta tags
	meta: [
	  { name: 'application-name', content: 'Federació de Pilota Valenciana' },
	  { name: 'description', content: 'La Federació de Pilota Valenciana és l\'entitat pública encarregada de la pilota valenciana en general. Entre d\'altres, reglamenta les diverses modalitats, n\'organitza competicions i promou la difusió de l\'esport autòcton i propi del País Valencià.', id: 'desc' }, // id to replace intead of create element
	  // ...
	  // Twitter
	  { name: 'twitter:title', content: 'Federació de Pilota Valenciana' },
	  // with shorthand
	  { n: 'twitter:description', c: 'La Federació de Pilota Valenciana és l\'entitat pública encarregada de la pilota valenciana en general'},
	  // ...
	  // Google+ / Schema.org
	  { itemprop: 'name', content: 'Federació de Pilota Valenciana' },
	  { itemprop: 'description', content: 'La Federació de Pilota Valenciana és l\'entitat pública encarregada de la pilota valenciana en general' },
	  // ...
	  // Facebook / Open Graph
	  //{ property: 'fb:app_id', content: '123456789' },
	  //{ property: 'og:title', content: 'Content Title' },
	  // with shorthand
	  //{ p: 'og:image', c: 'https://example.com/image.jpg' },
	  // ...
	],
	// link tags
	link: [
	  { rel: 'canonical', href: 'http://fedpival.es/', id: 'canonical' },
	  { rel: 'author', href: 'indiza', undo: false }, // undo property - not to remove the element
	  { rel: 'icon', href: '/static/logo.png', sizes: '16x16', type: 'image/png' }, 
	  // with shorthand
	  //{ r: 'icon', h: '../static/logo.png', sz: '32x32', t: 'image/png' },
	  // ...
	],
	script: [
	  //{ type: 'text/javascript', src: 'cdn/to/script.js', async: true, body: true}, // Insert in body
	  // with shorthand
	  //{ t: 'application/ld+json', i: '{ "@context": "http://schema.org" }' },
	  // ...
	],
	style: [
	  //{ type: 'text/css', inner: 'body { background-color: #000; color: #fff}', undo: false },
	  // ...
	]
}