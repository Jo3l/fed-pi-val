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
						<td v-for="column in tableColumns">
							<div v-if="!column.html"> {{ collect(row, column.field) }} </div>
							<div v-if="column.html" v-html="collect(row, column.field)"></div>						
						</td>
						<td class="actions">
							<slot name="actions" :row="row"></slot>
						</td>
						
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
		collect: function(obj, field) {
			if (typeof(field) === 'function')
				return field(obj);
			else if (typeof(field) === 'string')
				return this.dig(obj, field);
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
			return result;
		},
	},
	mounted: function () {

	}
}
</script>

<style lang="less">

@import "../../assets/less/defines.less";


</style>