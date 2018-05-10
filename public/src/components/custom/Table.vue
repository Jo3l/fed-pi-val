<template>
    <transition name="fade">
         <div class="table-responsive">
            <table class="table">
               <thead>
					<tr>
						<th v-for="(column, index) in tableColumns">
							{{column.label}}
						</th>
						<slot name="headActions"></slot>
					</tr>
               </thead>
               <tbody>
					<tr v-for="(row, index) in tableList">
						
						<slot name="icon1" :row="row"></slot>
						<slot name="icon2" :row="row"></slot>
						
						<td v-for="column in tableColumns" v-if="!column.icon">
							<div v-if="!column.html"> {{ collect(row, column) }} </div>
							<div v-if="column.html" v-html="collect(row, column)"></div>						
						</td>
						
						<slot name="actions" :row="row"></slot>
						
					</tr>
               </tbody>
            </table>
         </div>
    </transition>
</template>

<script>


export default {
	name: 'Table',
  	components: {},
  	props: ['tableList', 'tableColumns'],
	data () {
		return {

		}
	},
	methods: {
		collect: function(obj, column) {
			
			console.log(column.icon);
			
			if (typeof(column.field) === 'function')
				return field(obj);
			else if (typeof(column.field) === 'string')
				return this.dig(obj, column.field);
			else
				return undefined;
		},
		dig: function(obj, selector) {
			var result = obj;
			const splitter = selector.split('.');
			for (let i = 0; i < splitter.length; i++){
				if (result == undefined)
					return undefined;
					
				result = result[splitter[i]];
			}
			return result!=undefined ? result.toLowerCase() : result;
		},
	},
	mounted: function () {

	}
}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";

td {text-transform:capitalize;}

</style>