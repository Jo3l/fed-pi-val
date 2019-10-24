const state = {}

const getters = {

    validate: function (state) {
    	return function (n) {

			var DNI_REGEX = /^(\d{8})([A-Z])$/;
			var CIF_REGEX = /^([ABCDEFGHJKLMNPQRSUVW])(\d{7})([0-9A-J])$/;
			var DNINIE_REGEX = /^[XYZ]?\d{7,8}[A-Z]$/;
			var EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			
			if(n.type!='emailnull' && (n.string==null||n.string=='')) return true;
			
			if(n.type=='cif') {
				return CIF_REGEX.test(n.string.toUpperCase())==false;
			}
			else if(n.type=='dni') {
				return DNINIE_REGEX.test(n.string.toUpperCase())==false;
			}
			else if(n.type=='email') {
				return EMAIL_REGEX.test(n.string.toUpperCase())==false;
			}
			else if(n.type=='emailnull') {
				var res= false;
				try{
					res=EMAIL_REGEX.test(n.string.toUpperCase());
				}catch(e){console.log(e);}
				return n.string!='' && res==false;
			}
			else if(n.type=='not-null') {
				return !n.string.length>0;
			}
			else {
				return true;
			}
    	}

    }
    
}


const mutations = {

}

const actions = {

}

export default {
  state,
  getters,
  mutations,
  actions
}
