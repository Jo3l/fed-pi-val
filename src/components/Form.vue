<template>
    <transition name="fade">
    	
		<div class="dataeditor" >
			<div class="form">
			  <h1>{{ $t('form.nomtabla') }}</h1>
			  <h2>{{count}} / €</h2>

			</div>

		</div>

    </transition>
</template>

<script>

/*
http://fedpival.indiza.com/api/index.php/struct
{"club":{"nom":"varchar(50)","dir":"varchar(100)","cp":"char(5)","poblacio":"varchar(50)","creacio":"varchar(14)","json":"text"},"competicio":{"modalitat":"int(11)",...}
*/

export default {
    name: 'Form',
  	components: {},
	data () {
		return {
			struct: '',
		}
	},
	methods: {
		getData: function(apiUrl) {
	        var vm = this;
	        this.$http.get(apiUrl)
	        .then(function (response) {
	            vm.struct = response.data;
	            console.log(vm.struct);
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    }
	},
	mounted: function () {
		if(this.struct == '') this.getData('/struct');
	}}
</script>

<style lang="less">

@import "../assets/less/defines.less";
	

.dataeditor {
	.products {
		.item {
		  text-align: center;
		  padding:10px 0;
		  img {
		  	width:60%;
		  }
		}
		.name {
		  margin-bottom: 0.5em;
		}
		.price {
		  display: inline-block;
		  vertical-align: middle;
		}
	}
	
	.form {
	  margin: 1em 0 0;
	  width: 270px;
	}
	&.full {
		padding:20px;
		@media(max-width:@screenMobile) {
			padding:0;
		}
		.products {
			  display: flex;
			  flex-flow: row wrap;
			  justify-content: space-between;
			  padding: 0 20px;
			  &:after {
			  content: "";
			  flex: auto;
			  }
			.item {
				width: 25%;
				padding: 20px 0;

				@media(max-width:@screenTablet) {
					width: 33%;
				}

				@media(max-width:@screenMobile) {
					width: 50%;
				}
			
				img {
					width:75%;
				}
			}
		}
	}
}

</style>