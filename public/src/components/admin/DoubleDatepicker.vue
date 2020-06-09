<template>

	<div class="input2flex">

			<ui-datepicker
                :placeholder="$i18n.t('calendar.dateTip')"
                :start-of-week="datePickerOptions.dow"
                v-model="insInici"
                :lang="datePickerOptions"
            >Data Inici Inscripció:</ui-datepicker>

			<ui-datepicker
                :placeholder="$i18n.t('calendar.dateTip')"
                :start-of-week="datePickerOptions.dow"
                v-model="insFinal"
                :lang="datePickerOptions"
            >Data Final Inscripció:</ui-datepicker>

	</div>
    
</template>

<script>

export default {
  	components: { },
  	props: ['insInici',	'insFinal'],
	data () {
		return {
		    datePickerOptions: {
			  dow: Number(eval(this.$parent.$i18n.t('calendar.mondayFirst'))),
			  months: {
			    full: this.$parent.$i18n.t('calendar.months'),
			    abbreviated: this.$parent.$i18n.t('calendar.monthsShort')
			  },
			  days: {
			    full: this.$parent.$i18n.t('calendar.weekLong'),
			    abbreviated: this.$parent.$i18n.t('calendar.weekShort'),
			    initials: this.$parent.$i18n.t('calendar.weekInitials')
			  }
			},
		}
	},
	methods: {
	    fixDateForParse: function (date) { 
			  if(date) return Date.parse(date.substr(0, 4) + '.' + date.substr(4, 2) + '.' + date.substr(6,2) + ' ' + date.substr(8,2) + ':' + date.substr(10,2) + ':' + date.substr(12) );
		},
	},
	mounted: function () {
		var vm=this;
		
		if(vm.insInici=='') vm.insInici=new Date;
		if(vm.insFinal=='') vm.insFinal=new Date;
	},
	watch: {

	},
	computed: {

	    
	},
	created() {

	},
	beforeDestroy() {

	}
}
</script>


<style lang="less">

@import "../../assets/less/defines.less";


</style>