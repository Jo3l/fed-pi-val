<template>
    <transition name="fade">
    	
		<carousel :perPageCustom="[[0, 1],[480, 1],[768, 2],[992, 2], [1200, 3]]" :minSwipeDistance=30>
			
		  <slide>
		    <article style="background-image:url(/static/dev/foto2.jpg);">
		    	<div class="articleContainer">
			    	<small>30/05/2017</small>
			    	<h2>Multitudinària Trobada de Pilota a l'escola a Massalfassar</h2>
			    	<section>
			    		<p>El Poliesportiu municipal de Massalfassar acollia este matí la cloenda, en este cas, segona ja després de la Borriana, dins del programa Pilota a l’Escola de la Conselleria d’Educació, Investigació, Cultura i Esport.</p>
			    	</section>
		    	</div>
		    </article>
		  </slide>

		  <slide>
		    <article style="background-image:url(/static/dev/foto3.jpg);">
		    	<div class="articleContainer">
			    	<small>30/05/2017</small>
			    	<h2>Multitudinària Trobada de Pilota a l'escola a Massalfassar</h2>
			    	<section>
			    		<p>El Poliesportiu municipal de Massalfassar acollia este matí la cloenda, en este cas, segona ja després de la Borriana, dins del programa Pilota a l’Escola de la Conselleria d’Educació, Investigació, Cultura i Esport.</p>
			    	</section>
		    	</div>
		    </article>
		  </slide>	
		  
		  <slide>
		    <article style="background-image:url(/static/dev/foto4.jpg);">
		    	<div class="articleContainer">
			    	<small>30/05/2017</small>
			    	<h2>Multitudinària Trobada de Pilota a l'escola a Massalfassar</h2>
			    	<section>
			    		<p>El Poliesportiu municipal de Massalfassar acollia este matí la cloenda, en este cas, segona ja després de la Borriana, dins del programa Pilota a l’Escola de la Conselleria d’Educació, Investigació, Cultura i Esport.</p>
			    	</section>
		    	</div>
		    </article>
		  </slide>

		  <slide>
		    <article style="background-image:url(/static/dev/foto1.jpg);">
		    	<div class="articleContainer">
			    	<small>30/05/2017</small>
			    	<h2>Multitudinària Trobada de Pilota a l'escola a Massalfassar</h2>
			    	<section>
			    		<p>El Poliesportiu municipal de Massalfassar acollia este matí la cloenda, en este cas, segona ja després de la Borriana, dins del programa Pilota a l’Escola de la Conselleria d’Educació, Investigació, Cultura i Esport.</p>
			    	</section>
		    	</div>
		    </article>
		  </slide>

		  <slide>
		    <article style="background-image:url(/static/dev/foto2.jpg);">
		    	<div class="articleContainer">
			    	<small>30/05/2017</small>
			    	<h2>Multitudinària Trobada de Pilota a l'escola a Massalfassar</h2>
			    	<section>
			    		<p>El Poliesportiu municipal de Massalfassar acollia este matí la cloenda, en este cas, segona ja després de la Borriana, dins del programa Pilota a l’Escola de la Conselleria d’Educació, Investigació, Cultura i Esport.</p>
			    	</section>
		    	</div>
		    </article>
		  </slide>

		  <slide>
		    <article style="background-image:url(/static/dev/foto3.jpg);">
		    	<div class="articleContainer">
			    	<small>30/05/2017</small>
			    	<h2>Multitudinària Trobada de Pilota a l'escola a Massalfassar</h2>
			    	<section>
			    		<p>El Poliesportiu municipal de Massalfassar acollia este matí la cloenda, en este cas, segona ja després de la Borriana, dins del programa Pilota a l’Escola de la Conselleria d’Educació, Investigació, Cultura i Esport.</p>
			    	</section>
		    	</div>
		    </article>
		  </slide>
		</carousel>

    </transition>
</template>

<script>

export default {
  name: 'Noticias',
  data () {
    return {
        pagina: 1,
        links:{},
        tag: {},
        tagNames: {},
        users: {}
    }
  },
  methods: {
  	dayGroup: function(dateStr) {
  		
  		return dateStr.split(' ')[0]
  		
  		if (vm.day != dateStr.split(' ')[0]) {
  			//vm.day = dateStr.split(' ')[0];
  			//return true;
  		} else {
			//return false;
  		}
  	},
  	loadingBar: function(data){
			this.$parent.$emit('loadingBar', data);
	},
    getLinks: function() {

        var vm = this;
		vm.loadingBar(true);
        this.$http.get('')
        .then(function (response) {
            
            console.log(response);
            vm.links = response.data.links;
            vm.tag = response.data.tag;
            vm.tagNames = response.data.tagNames;
            vm.users = response.data.users;
            vm.loadingBar(false);
            
            console.log(vm.links);
            
            
        })
        .catch(function (error) {
            console.log(error);
        });
        
    },
    post: function() {
    	console.log(this.links);
    	this.$http.post('', {'user':'joel', 'password':'cibermatch'} ).then(function (response) { console.log(response) });
    }
  },
    mounted: function () {
		this.getLinks();
    },
    watch: {

    }
}
</script>

<style lang="less">

.VueCarousel {
	article {
		height:400px;
		overflow:hidden;
		background-size:cover;
		background-position:center;
		display:flex;
		flex-direction: column;
		justify-content:flex-end;
		border-left: 5px solid white;
		border-left: 5px solid white;
		
		.articleContainer {
			background-image: linear-gradient(rgba(0, 0, 0, 0) 0%, #ffffff 41%);
    		padding: 40px 20px 20px 20px;
		}
		
		h2 {margin:0;line-height:1em;}
		
		p {margin:0;}
	
		}
}

</style>
