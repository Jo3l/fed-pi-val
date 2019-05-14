<template>
    <transition name="fade">
<div v-bind:class="{ 'alert':true, 'visible':!alertVisible }" v-if="!readCookie('seen-alert-message')">
	
    <div class="consent-content">
        <p>{{$i18n.t('common.oldweb')}} <a href="" target="_blank">http://antiga.fedpival.es</a></p>
    </div>
    <div class="consent-action">
         <button @click="acceptCookie" class="button cta"> {{$i18n.t('modal.close')}} </button>
    </div>
</div>
    </transition>
</template>

<script>


export default {
	name: 'Alert',
  	components: {},
  	props: [],
	data () {
		return {
			alertVisible:false,
		}
	},
	methods: {
		acceptCookie: function() {
			var vm=this;
			vm.alertVisible = true;
			vm.createCookie('seen-alert-message','true','60','/');
		},
		createCookie: function(name,value,days,path) {
	        if (days) {
	            var date = new Date();
	            date.setTime(date.getTime()+(days*24*60*60*1000));
	            var expires = "; expires="+date.toGMTString();
	        }
	        else var expires = "";
	        document.cookie = name+"="+value+expires+"; path="+path;
	    },
	    readCookie: function(name) {
	        var nameEQ = name + "=";
	        var ca = document.cookie.split(';');
	        for(var i=0;i < ca.length;i++) {
	            var c = ca[i];
	            while (c.charAt(0)==' ') c = c.substring(1,c.length);
	            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	        }
	        return null;
	    }
	},
	mounted: function () {

	}
}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";

.alert {
    position: fixed;
	left: 0;
    top: 0;
	background-color: rgb(132, 33, 47);
    padding: 5px 5%;
    z-index:99;
    display:none;
    color: white;

}
.alert.visible {
    display:flex;
    justify-content: center;
    align-items: center;
    box-sizing: border-box;
    width: 100%;
    box-shadow: 0px 0px 30px rgba(0,0,0,0.2);
}


.alert .consent-content {
    p{
        margin: 0;
        font-size: 16px;
        margin-right: 15px;
        a, a:hover, a:visited {color:white!important; text-decoration:underline!important;}
        
    }

}

.alert .consent-info {
    color: white;
    padding: 25px 0;
    margin: 0;
    opacity: 1;
    transition: color 500ms ease;
    text-transform: capitalize;
    font-weight: bolder;
    text-decoration: underline;
}
.alert .consent-action {
    min-width:200px;
}
.alert .consent-action .button{
	border: none;
    height: 42px;
    line-height: 32px;
    padding: 0 12px;
    font-size: 16px;
    font-weight: normal;
    font-style: normal;
    display: inline-block;
    text-decoration: none;
    color: #FFF;
    transition: all .3s ease;
    cursor:pointer;
    &.cta {
		height: initial;
	    font-size: 14px;
	    border-radius: 8px;
	    color:@fedcolor;
	    background-color: white;
	    display: block;
	    text-align: center;
    }
}

@media (max-width: 768px) {
    .alert.visible {
        display: block;
        padding: 25px 6%;
    }
    .alert p {
        font-size: 14px;
        text-align: left;
    }
    .alert .consent-action {
        padding:0;
        display: flex;
        flex-wrap: wrap; 
        min-width:0px;
        .button{width:100%;}
    }
    
    .consent-action {
        display: block !important;
        margin-top: 10px;
    }
    .alert .consent-info {
        order: 1;
        margin: 0;
        width: 100%;
        text-align: center;
    }
}




</style>