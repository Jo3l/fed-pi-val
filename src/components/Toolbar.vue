<template>

<div id="toolbarContainer">
	
	<ui-toolbar
        brand="Federació de Pilota Valenciana"
        title=""
        type="clear"
        :raised="false"
    >
		<div slot="icon">
			<img src="/static/logo.png">
		</div>
	    <div slot="actions">
            <ui-icon-button
                color="black"
                icon="search"
                size="large"
                type="secondary"
                @click="toggleBar()"
            ></ui-icon-button>
	    </div>
	    
	</ui-toolbar>

	<div class="mainMenu">
	    <input id="menu-toggle" type="checkbox">
	    <label class="menu-button-container" for="menu-toggle">
	    	<div class="menu-button"></div>
	  	</label>
	    <ul class="menu">
			<li>Inici</li>
			<li>Noticies</li>
			<li>Competicions</li>
			<li>Calendari</li>
			<li>Botiga</li>
			<li>Buscador</li>
			<li>Contacte</li>
	    </ul>
	</div>

   	<div class="fancyEnd">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 350 20" preserveAspectRatio="none" enable-background="new 0 0 350 20" xml:space="preserve">
			<path stroke="null" id="svg_1" fill="rgba(45,85,170,0.5)" d="m0,2.21516c29.9,0 57.8,14.53465 87.5,14.53465c30.2,0 58.1,-14.53465 87.1,-14.53465c29.9,0 57.8,14.53465 87.7,14.53465c29.9,0 57.8,-14.53465 87.7,-14.53465l0,-2.46494l-350,0l0,2.46494z"/>
		</svg>
	</div>
	
</div>
</template>

<script>

export default {
	name: 'Toolbar',
	data () {
	return {
		loadingBar: false,
		menuOptions: [
		    {
		        label: 'Settings'
		    },
		    {
		        label: 'About'
		    },
		    {
		        label: 'Help'
		    }
		]
	}
	},
	methods: {
		toggleBar: function() {
		this.loadingBar = !this.loadingBar;
		console.log(this.loadingBar);
		}
	
	},
	created: function(){
		var vm = this;
		this.$parent.$on('loadingBar', function(data){
			vm.loadingBar = data;
		})
	},
    watch: {

    }
}
</script>

<style lang="less">
	


</style>