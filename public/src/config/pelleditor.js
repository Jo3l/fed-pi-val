import VuePellEditor from 'vue-pell-editor'

export default function(openModal) {
    return [
              {
              	name: 'Undo',
              	icon: '<i class="material-icons">undo</i>',
              	title:'Desfer',
              	result: () => VuePellEditor.components.pell.exec('undo')
              },
              {
              	name: 'Redo',
              	icon: '<i class="material-icons">redo</i>',
              	title:'Refer',
              	result: () => VuePellEditor.components.pell.exec('redo')
              },
              {
              	name: '',
              	title:'',
              	icon: '',
              },
              {
                name: 'bold',
                icon: '<i class="material-icons">format_bold</i>',
              	title:'Negreta',
                result: () => VuePellEditor.components.pell.exec('bold')
              },
              {
                name: 'underline',
                icon: '<i class="material-icons">format_underlined</i>',
              	title:'Subratllat',
                result: () => VuePellEditor.components.pell.exec('underline')
              },
              {
                name: 'UnOrdered List',
                icon: '<i class="material-icons">list</i>',
              	title:'Llista',
                result: () => VuePellEditor.components.pell.exec('insertUnorderedList')
              },
              {
                name: 'UnOrdered List',
                icon: '<i class="material-icons">format_list_numbered</i>',
              	title:'Llista ordenada',
                result: () => VuePellEditor.components.pell.exec('insertOrderedList')
              },
              {
                name: 'Indent',
                icon: '<i class="material-icons">format_indent_increase</i>',
              	title:'Identar',
                result: () => VuePellEditor.components.pell.exec('indent')
              },
              {
                name: 'Outdent',
                icon: '<i class="material-icons">format_indent_decrease</i>',
              	title:'Desidentar',
                result: () => VuePellEditor.components.pell.exec('outdent')
              },
              {
              	name: '',
              	icon: '',
              	title:'',
              },
              {
              	name: 'Align Left',
              	icon: '<i class="material-icons">format_align_left</i>',
              	title:'Ajustar Esquerre',
              	result: () => VuePellEditor.components.pell.exec('justifyLeft')
              },
              {
              	name: 'Align Center',
              	icon: '<i class="material-icons">format_align_center</i>',
              	title:'Ajustar Centre',
              	result: () => VuePellEditor.components.pell.exec('justifyCenter')
              },
              {
              	name: 'Align Right',
              	icon: '<i class="material-icons">format_align_right</i>',
              	title:'Ajustar Dreta',
              	result: () => VuePellEditor.components.pell.exec('justifyRight')
              },
              {
              	name: 'Justify',
              	icon: '<i class="material-icons">format_align_justify</i>',
              	title:'Justificar',
              	result: () => VuePellEditor.components.pell.exec('justifyFull')
              },
              {
              	name: '',
              	icon: '',
              },
              {
                name: 'image',
                icon: '<i class="material-icons">insert_photo</i>',
              	title:'Insertar Imatge',
                result: () => {
                  openModal('uploadModal', {url:''}, '', 'img');
                  window.recoverFocus=document.activeElement.parentNode.nextElementSibling;
                  //VuePellEditor.components.pell.exec('insertImage', this.selected.url);
                }
              },
              {
                name: 'link',
                icon: '<i class="material-icons">link</i>',
              	title:'Insertar Link',
                result: () => {
                  const url = window.prompt('URL:');
                  if (url) VuePellEditor.components.pell.exec('createLink', url)
                }
              },
              {
              	name: '',
              	icon: '',
              	title:'',
              },
              {
              	name:'clear',
              	icon: '<i class="material-icons">format_clear</i>',
              	title:'Borrar format',
              	result: () => {
              		//var String = Sample.replace(/(<([^>]+)>)/ig,"");
              		console.log(VuePellEditor.components.pell);
              		VuePellEditor.components.pell.exec('removeFormat');
              	}
              }

            ];
}
