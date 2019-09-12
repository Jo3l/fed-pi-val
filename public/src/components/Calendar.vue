<template>
	<transition name="fade">
	<div class="componentContainer">
		<div v-if=" type=='slider' ">
			<h1>{{ $t('common.calendar') }}</h1>
			<div class="buttonContainer">
				<ui-icon-button @click="decMonth" icon="chevron_left" type="primary" class="buttonFloatLeft"></ui-icon-button>
				<ui-icon-button @click="incMonth" icon="chevron_right" type="primary" class="buttonFloatRight"></ui-icon-button>
			</div>
	
	
			<div class="calendarContainer">
				<div class="calendar" v-for="n in 4">
					<nav class="calendarHead">
		
						<b>{{ monthArray[getMonth(n-1+increment)] }}</b>
						<b>{{ getYear(n-1+increment)}}</b>
		
					</nav>
					<div class="labels">
						<span class="calendarLabel" v-for="label in dayLabelsFixed">{{ label }} </span>
					</div>
					<div class="calendarMonth">
						<div v-for="week in events[eventos(n-1+increment)]">
							<span class="calendarDay" v-for="day in week" v-bind:class="[selectedClass(day), dayClass(day)]">
								<span v-if="day.date" v-bind:class="day.data.cssClass"  @click="openModalDay( day )">
									{{ day.date }}
								</span>
							</span>
						</div>
					</div>
				</div>
			</div>
			<ui-modal ref="events" size="normal" title="titol">
	            <div slot="header">
	                {{selected.day}} - {{selected.month+1}} - {{selected.year}}
	                
		
	            </div>
				<article v-for="event in modalEvent">
					<h4>{{event.titol}}</h4>
					<p v-html="event.contingut"></p>
				</article>
	        </ui-modal>
        </div>
        
        
        
        
		<div v-else="v-else">
			<h1>{{ $t('common.calendar') }}
				<ui-button v-if="$store.getters.isAuthenticatedWithRole(0)" color="blueButtonToRight" icon="save" size="small" type="secondary" @click="createBlock()">{{ $t('common.eventNew') }}</ui-button>
			</h1>
			<div class="buttonContainer">
				<ui-icon-button @click="decMonth" icon="chevron_left" type="primary" class="buttonFloatLeft"></ui-icon-button>
				<ui-icon-button @click="incMonth" icon="chevron_right" type="primary" class="buttonFloatRight"></ui-icon-button>
			</div>
	
	
			<div class="calendarContainer">
				<div class="calendar" v-for="n in 4">
					<nav class="calendarHead">
		
						<b>{{ monthArray[getMonth(n-1+increment)] }}</b>
						<b>{{ getYear(n-1+increment)}}</b>
		
					</nav>
					<div class="labels">
						<span class="calendarLabel" v-for="label in dayLabelsFixed">{{ label }} </span>
					</div>
					<div class="calendarMonth">
						<div v-for="week in events[eventos(n-1+increment)]">
							<span class="calendarDay" v-for="day in week" v-bind:class="[selectedClass(day), dayClass(day)]">
								<span v-if="day.date" v-bind:class="day.data.cssClass" @click="openEvent( day )">
									{{ day.date }}
								</span>
							</span>
						</div>
					</div>
				</div>
			</div>
			
			<div class="eventSelected">
				<article v-for="event in todayEvent">
					
					<aside v-if="$store.getters.isAuthenticatedWithRole(0)" class="nodeContentElement lined">
						
						<h4>{{parseTime(event.publicacio)}}</h4>
						<i class="remove" @click="removeContent(event, todayEvent)"></i>
						
						<h4>Codi de color:</h4>
						<!--
						<ui-icon-button color="primary" :icon="event.color=='primary'?'done':''" size="small" @click="event.color='primary'"></ui-icon-button>
						<ui-icon-button color="accent"  :icon="event.color=='accent'?'done':''"size="small" @click="event.color='accent'"></ui-icon-button>
						<ui-icon-button color="orange" :icon="event.color=='orange'?'done':''"size="small" @click="event.color='orange'"></ui-icon-button>
						<ui-icon-button color="red" :icon="event.color=='red'?'done':''"size="small" @click="event.color='red'"></ui-icon-button>
						<ui-icon-button color="green" :icon="event.color=='green'?'done':''"size="small" @click="event.color='green'"></ui-icon-button>
						-->
							<ui-textbox
							    floating-label
				                autocomplete="off"
				                error="This field is required"
				                label="Títol:"
								type="text"
				                v-model="event.titol"
							></ui-textbox>
							
						    <VuePellEditor 
						        :actions="editorOptions" 
						        :content="event.contingut" 
						        v-model="event.contingut"
						        :styleWithCss="false"
								placeholder="..."
						    />
							
							<ui-button color="blueButtonToRight" icon="save" size="small" type="secondary" @click="saveBlock(event)">{{$i18n.t('common.save')}}</ui-button>
							<ui-button color="blueButtonToRight" icon="delete" size="small" type="secondary" @click="removeContent(event, todayEvent)">{{$i18n.t('common.delete')}}</ui-button>
					</aside>
					
					<aside v-if="!$store.getters.isAuthenticatedWithRole(1) && event.id">
						<h4>{{parseTime(event.publicacio)}} - {{event.titol}}</h4>
						<p v-html="event.contingut"></p>
					</aside>
					
				</article>
				
				
			</div>
			
		</div>
		
		
		<ui-modal size="largeSquare" ref="uploadModal" title="Media Manager">
			<filemanager ref="upload" v-bind:pselected="selected"></filemanager>
			<div slot="footer">
                <ui-button @click="acceptModal('uploadModal')" color="fedpival">{{$i18n.t('modal.ok')}}</ui-button>
                <ui-button @click="closeModal('uploadModal')">{{$i18n.t('modal.cancel')}}</ui-button>
            </div>
        </ui-modal>
		
	</div>
	</transition>
</template>

<script>

import VuePellEditor from 'vue-pell-editor'
import VuePellEditorConfig from '../config/pelleditor'
import FileManager from './FileManager.vue'

export default {
	name: 'Calendar',
  	components: { VuePellEditor, 'filemanager':FileManager	}, 
  	props: {
        type: {
            type: String,
            default: 'fullPage'
        }
	},
	data: function(){ 
		return {
			colorSelected:'',
			mondayFirst: eval(this.$parent.$i18n.t('calendar.mondayFirst')),
			dayLabels: this.$parent.$i18n.t('calendar.weekShort'),
			increment: 0,
			events: [],
			monthArray: this.$parent.$i18n.t('calendar.months'),
			selected : {
				day : false,
				month : false,
				year : false
			},
			modalEvent: [],
			todayEvent: [],
            editorOptions: VuePellEditorConfig(this.openModal),

		}
	},
	head : function() {
		return this.$route.meta;
	},
	computed: {
		dayLabelsFixed: function() {
			if(this.mondayFirst) {
				var b = this.dayLabels.shift();
				this.dayLabels.push(b);
			}
			return this.dayLabels;
		}
	},
	methods: {
		createBlock: function() {
			console.log(this.selected);
			this.todayEvent.push({
				idioma:this.$i18n.locale,
				tipus:"A",
				destacada:"0",
				categoria:"acte",
				tags:"",
				publicacio:Date.parse(this.selected.year+'/'+(this.selected.month+1)+'/'+this.selected.day).toString('yyyyMMddHHmmss'),
				titol:"",
				contingut:"",
				color:"",
				json: null
			});
		},
		removeContent: function(event, todayEvent) {
			var vm=this;
			console.log(event,todayEvent);
			vm.$http.delete('/acte/'+event.id)
	        .then(function (response) {
	        	
				for(var i = todayEvent.length; i--;) {
					var ev=todayEvent[i];
					if(ev.id===event.id) {
						todayEvent.splice(i, 1);
					}
				}

	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	        
		},
		saveBlock: function(block){
			
			var vm = this;
			var blocid = block.id ? block.id : '';
			block.json= block.json ? block.json : '';
			try {
				var json= JSON.parse(block.json);
				//json.color= block.color;
				//delete block.color;
				block.json=  JSON.stringify(json);
			} catch(e) { console.log(e); console.log(block.json); }

	        vm.$http.post('/acte/'+blocid, block)
	        .then(function (response) {
	        	
				//no cal fer res.
				vm.todayEvent= [];
				vm.selected= {
					day : false,
					month : false,
					year : false
				}

	        })
	        .catch(function (error) {
	            console.log(error);
	        });

		},
		parseTime: function(time) {
			
			var str = time;
			var year = str.substring(0, 4);
			var month = str.substring(4, 6);
			var day = str.substring(6, 8);
			var hour = str.substring(8, 10);
			var minute = str.substring(10, 12);
			var second = str.substring(12, 14);
			
			return new Date(year, month-1, day, hour, minute, second).toString("d/M/yyyy");	
			
		},
		openEvent: function(day) {
        	this.todayEvent = day.data.events;
        	this.select(day);
        },
        openModalDay: function(day) {
        	this.modalEvent = day.data.events;
        	this.select(day);
            if( this.modalEvent.length>0) {
            	this.$refs.events.open();
            }
        },
		openModal:function(ref, object, cancel, tipo) {
			this.$refs.upload.activate(tipo);
			this.selectedCancel = cancel;
			this.selected=object;
            this.$refs[ref].open();
        },
        acceptModal:function(ref) {
        	VuePellEditor.components.pell.exec('insertImage', this.selected.url);
        	this.selected={};
            this.$refs[ref].close();
        },
        closeModal:function(ref) {
        	this.selected.url = this.selectedCancel;
        	this.selected={};
            this.$refs[ref].close();
        },
		eventos: function(n) {
			var month = Date.today().add(n).month().getMonth();
			var year = Date.today().add(n).month().getYear() + 1900;
			var current = year+''+("0" + (month + 1)).slice(-2);
			return current;
		},
		calendar: function(n, force) {
			var squareIndex =  0;
			var mondayFirst = this.mondayFirst ? 1 : 0;
			var dateOfMonth = 1;
			var weeks = [];
			var month = Date.today().add(n).month().getMonth();
			var year = Date.today().add(n).month().getYear() + 1900;
			
			var firstDate = new Date(year, month, 1);
			var daysInMonth = Date.getDaysInMonth(year, month);
			var firstDayIndex = firstDate.getDay() - mondayFirst < 0 ? 6 : firstDate.getDay() - mondayFirst;
			var lastDate = firstDayIndex + daysInMonth;
			
			var current = year+''+("0" + (month + 1)).slice(-2);
		    var vm = this;
		    
		    if( vm.events[current] && !force ) return vm.events[current];
		    var refIndex = 1;
	        this.$http.get('actes/'+current+'/i/'+this.$i18n.locale)
	        .then(function (response) {
	        	var res = response;
		        while (squareIndex < lastDate) {
					var week = [];
					
					for (var d = 0; d < 7; d++) {
						if (squareIndex < firstDayIndex || squareIndex >= lastDate) {
							week.push({ date : false });
						} else {
							
							var eventsInDay=[];
							for (var event in res.data) {
								//var json= JSON.parse(res.data[event].json);
								//res.data[event].color= ( (json && json.color) ? json.color : 'primary');
								if(res.data[event].publicacio && res.data[event].publicacio == year+''+("0" + (month + 1)).slice(-2)+''+("0" + (dateOfMonth)).slice(-2)) {
									eventsInDay.push(res.data[event]);
	        					}
							}
							
							var clase = '';
							if( Object.keys(eventsInDay).length > 0 ) {
								clase = 'event';
							}
							
							//afegir clase color a mes de clase event
							
							if( Date.today().getDate()==dateOfMonth && Date.today().getMonth() == month && (Date.today().getYear()+1900) == year) {
								clase = 'today';
							}

							week.push({
								date : dateOfMonth, 
								data : {
									day : dateOfMonth,
									month : month,
									year : year,
									cssClass : clase,
									events: eventsInDay,
									index: refIndex
								}
							});
							dateOfMonth++;
							refIndex++;
							
						}
						squareIndex++;
						
					}
					weeks.push(week);
					
				}
				vm.events[current] = weeks;
				vm.events.push(vm.events.pop());

				return weeks;

	        })
	        .catch(function (error) {
	            console.log(error);
	        });

		},
		getMonth: function(n) {
			return Date.today().add(n).month().getMonth();
		},
		getYear: function(n) {
			return Date.today().add(n).month().getYear() + 1900;
		},
		incMonth: function() {
			this.increment++;
			this.calendar(this.increment + 3);
		},
		decMonth: function() {
			this.increment--;
			this.calendar(this.increment);

		},
		select : function(day){

			if(this.isSelected(day)){
				this.selected = {
					day : false,
					month : false,
					year : false 
				};
				return true;
			}
			
			return this.selected = {
				day : day.data.day,
				month : day.data.month,
				year : day.data.year 
			};
			
		},
		isSelected : function(day){
			if(day.date === false){
				return false;
			}
			if(
				this.selected.year == day.data.year && 
				this.selected.month == day.data.month &&
				this.selected.day == day.data.day){
				return true;
			}
			return false;
		},
		selectedClass : function(day){
			if(this.isSelected(day)){
				return 'selected';
			}
			return '';
		},
		dayClass : function(day){
			if(this.isDay(day)){
				return 'day';
			}
			return '';
		},
		isDay : function(day){
			return day.date !== false;
		}
	},
	mounted: function() {
		this.calendar(0);
		this.calendar(1);
		this.calendar(2);
		this.calendar(3);
		
		this.selected.year = Date.today().getYear() + 1900;
		this.selected.month = Date.today().getMonth();
		this.selected.day = Date.today().getDate();
		
	}
};
</script>


<style lang="less">

@import "../assets/less/defines.less";

.eventSelected {
	margin: 2em;
    min-height: 30px;
    article {
	    aside {
		    padding-bottom: 15px;
		    border-bottom: 1px dashed @fedcolor;
		    margin-top: 30px;
		    &.lined{
		    	border-bottom: 1px solid @fedcolor!important;
		    }
		    p > * {
		    	max-width:100%;
		    }
		}
		&:last-of-type{
			aside {
			border-bottom:none;
			}
		}
    }
    input {
	    width: 100%;
	    font-family: 'Rambla', cursive;
	    display: block;
	    font-size: 1.5em;
	    border: none;
	    border-bottom: 1px dashed #ccc;
	    //margin-bottom: 0.5em;
	    //min-height: 42px;
	    color: rgba(0, 0, 0, 0.87);
	}

}
	
.calendarContainer {
	display:flex;
	position: relative;
    width: 100%;
    justify-content:space-around;
    
	.calendarNav {
		cursor: pointer;
	}
    
    .calendar {
		padding: 0 15px;
		min-height: 240px;
		
		&:nth-child(4) {
			@media(max-width:@screenDesktop) {
				display:none;
			}
		}
		
		&:nth-child(3) {
			@media(max-width:@screenTablet) {
				display:none;
			}
		}
		
		&:nth-child(2) {
			@media(max-width:@screenMobile) {
				display:none;
			}
		}

	}
	.calendarHead,
	.calendarDay,
	.calendarLabel
	{
		user-select: none;
		text-align:center;
	}
	
	.calendarMonth {
		&>div {
		    display: flex;
		    flex-direction: row;
		    justify-content: center;
		}
		.ui-modal__container {
			article {
			    border-bottom: 1px solid #e1e1e1;
			    &:last-child {
			    	border-bottom:none;
			    }
			}
		}

	}
	
	.labels {
	    display: flex;
	    flex-direction: row;
	    justify-content: center;
	}
	
	
	span.calendarLabel{
	  display: inline-block;
	  box-sizing: border-box;
	  overflow: hidden;
	  text-align: center;
	  font-size:10px;
	  width:32px;
	  padding:5px 0;
	}
	
	span.calendarDay {
		display: inline-block;
		box-sizing: border-box;
		overflow: hidden;
		text-align: center;
		height: 32px;
		width: 32px;
		&.day {
		  cursor: pointer;
		  &:nth-last-child(2), &:last-child {
		    color: #ff7676;
		    box-sizing: content-box;
		  }
		}
		span {
			border-radius: 20px;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 	32px;
			width: 32px;
		  	&.today {
			    border: 2px dotted rgba(0,0,0,0.5);
			    color:black;
			}
		  	&.event {
				background-color: #ffeef0;
    			border: 1px solid white;
    			&.primary {
    				background-color: #2196f3;
    			}
    			&.accent {
    				background-color: #a100bc;
    			}
    			&.orange {
    				background-color: #c27400;
    			}
    			&.red {
    				background-color: #e11b0c;
    			}
    			&.green {
    				background-color: #39843c;
    			}
			}
		}
		
		
		&.selected {
			span {
				background: #2ecc71;
			    color: #fff;
			    font-weight: bold;
			}
		}
	
	}

}


</style>