<template>
    <transition name="fade">
    	
		<carousel :perPageCustom="[[0, 1],[480, 1],[768, 2],[992, 2], [1200, 3]]" :minSwipeDistance=30 class="newsCarousel">
			
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
        news:{},
    }
  },
  methods: {
  	loadingBar: function(data){
			this.$parent.$emit('loadingBar', data);
	},
    getData: function() {

        var vm = this;
		vm.loadingBar(true);
        this.$http.get('/noticia')
        .then(function (response) {
            vm.news = response.data;
            vm.loadingBar(false);
            
            console.log(vm.news);
        })
        .catch(function (error) {
            console.log(error);
        });
        
        //console.log(vm.news);
    }
  },
    mounted: function () {
		this.getData();
    },
    watch: {

    }
}
</script>

<style lang="less">

.newsCarousel {
	article {
	    min-height: 400px;
	    overflow: hidden;
	    background-size: 130%;
	    background-position: top;
	    display: flex;
	    flex-direction: column;
	    justify-content: flex-end;
	    background-repeat: no-repeat;
	    border-right: 2px solid white;
    	border-left: 2px solid white;
		
		.articleContainer {
			background-image: linear-gradient(rgba(0, 0, 0, 0) 0%, #ffffff 41%);
    		padding: 40px 20px 20px 20px;
		}
		
		small {text-shadow: 1px 1px 1px white;}
		
		h2 {margin:0;line-height:1em;text-shadow: 1px 1px 1px white;}
		
		p {margin:0;}
	
		}
}

</style>
