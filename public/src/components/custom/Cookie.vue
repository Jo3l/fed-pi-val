<template>
    <transition name="fade">
<div v-bind:class="{ 'consent-cookies':true, 'visible':!cookieLegal }" v-if="!readCookie('seen-cookie-message')">
	
	<div class="waveHorizontals">
		<svg id="waveHorizontal1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 350 20" preserveAspectRatio="none" enable-background="new 0 0 350 20" xml:space="preserve">
			<path d="M0,17.1C29.9,17.1,57.8,0,87.5,0c30.2,0,58.1,17.1,87.1,17.1c29.9,0,57.8-17.1,87.7-17.1	c29.9,0,57.8,17.1,87.7,17.1V20H0V17.1z" fill="rgba(69,121,226,0.5)" />
		</svg>
		<svg id="waveHorizontal2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 350 20" preserveAspectRatio="none" enable-background="new 0 0 350 20" xml:space="preserve">
			<path d="M0,17.1C29.9,17.1,57.8,0,87.5,0c30.2,0,58.1,17.1,87.1,17.1c29.9,0,57.8-17.1,87.7-17.1	c29.9,0,57.8,17.1,87.7,17.1V20H0V17.1z" fill="rgba(52,97,193,0.5)" />
		</svg>
		<svg id="waveHorizontal3" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 350 20" preserveAspectRatio="none" enable-background="new 0 0 350 20" xml:space="preserve">
			<path d="M0,17.1C29.9,17.1,57.8,0,87.5,0c30.2,0,58.1,17.1,87.1,17.1c29.9,0,57.8-17.1,87.7-17.1	c29.9,0,57.8,17.1,87.7,17.1V20H0V17.1z" fill="rgba(45,85,170,0.5)" />
		</svg>
	</div>
	
    <div class="consent-content">
        <p>{{$i18n.t('common.cookieLegal')}} <router-link  class="consent-info" :to="{ path: '/val/federacio/politica-de-cookies' }"> política de cookies. </router-link></p>
    </div>
    <div class="consent-action">
         <button @click="acceptCookie" class="button cta"> {{$i18n.t('modal.ok')}} </button>
    </div>
</div>
    </transition>
</template>

<script>


export default {
	name: 'Cookie',
  	components: {},
  	props: [],
	data () {
		return {
			cookieLegal:false,
		}
	},
	methods: {
		acceptCookie: function() {
			var vm=this;
			vm.cookieLegal = true;
			vm.createCookie('seen-cookie-message','true','60','/');
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

.consent-cookies {
    position: fixed;
	left: 0;
    bottom: 0;
	background-color: rgb(132, 33, 47);
    padding: 5px 5%;
    z-index:99;
    display:none;
    color: white;
    .waveHorizontals{
    	top:-20px;
    	position: fixed;
    	transform: inherit;
    	left: 0;
    	height: 20px;
	}
}
.consent-cookies.visible {
    display:flex;
    justify-content: center;
    align-items: center;
    transform: translate3d(0, 3000px, 0);
    animation: 1.5s inUp 0.3s ease;
    animation-fill-mode: forwards;
    box-sizing: border-box;
    width: 100%;
    box-shadow: 0px 0px 30px rgba(0,0,0,0.2);
}


.consent-cookies .consent-content {
    p{
        margin: 0;
        font-size: 16px;
        margin-right: 15px;
    }
}

.consent-cookies .consent-info {
    color: white;
    padding: 25px 0;
    margin: 0;
    opacity: 1;
    transition: color 500ms ease;
    text-transform: capitalize;
    font-weight: bolder;
    text-decoration: underline;
}
.consent-cookies .consent-action {
    min-width:200px;
}
.consent-cookies .consent-action .button{
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
    .consent-cookies.visible {
        display: block;
        padding: 25px 6%;
    }
    .consent-cookies p {
        font-size: 14px;
        text-align: left;
    }
    .consent-cookies .consent-action {
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
    .consent-cookies .consent-info {
        order: 1;
        margin: 0;
        width: 100%;
        text-align: center;
    }
}


@keyframes inUp {

    from {
        opacity: 0;
        transform: translate3d(0, 3000px, 0);
    }

    to {
        transform: translate3d(0, 0, 0);
    }
}


</style>