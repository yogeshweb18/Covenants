<template>
	<div id="app">
		<form id="compliance_form" class="dark:text-white text-lg opacity-70" enctype="multipart/form-data" style="min-height: 300px" @submit.prevent="saveTimelines()">
	      <div class="mb-8 space-y-4"></div>
	        <h1 class="text-90 font-normal text-xl md:text-2xl mb-3">Edit Reminder Timelines</h1>

	        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
	        	<div v-if="Object.keys(covData).length>0">
               		<div class="title covenantheading">
				      <div>
				        <b>{{ covData.subType }}</b>
				      </div>
				    </div>
				    <div class="description accordionbox">
				      <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
			            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">Start Date (Allotment Date)
			              </label>
			            </div>
			            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">{{covData.startDate}}
			              </label>
			            </div>
			          </div>
			          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
			            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">End Date (Redemption Date)
			              </label>
			            </div>
			            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">{{covData.dueDate}}
			              </label>
			            </div>
			          </div>
			          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
			            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">Applicable Month
			              </label>
			            </div>
			            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
			              <label for="" class="inline-block pt-2 leading-tight label">{{covData.applicableMonth}}
			              </label>
			            </div>
			          </div>

			          <table class="w-full table-default" cellpadding="0" cellspacing="0" data-testid="resource-table" v-if="typeof covData.instances !== 'undefined' && Object.keys(covData.instances).length>0"  >
						    <thead class="bg-gray-50 dark:bg-gray-800">
						    	<tr>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Instance#</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Activate Date</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Due Date</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Applicable Month</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Status</span></th>
						         </tr>
						    </thead>
						    <tbody>
						    	<tr dusk="3-row" class="group" v-for="instance in covData.instances">
						    		<td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="instance.instanceNo" /><span class="text-90 whitespace-nowrap">{{instance.instanceNo}}</span></div>
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <input type="date" v-model="instance.activateDate" class="form-control form-input form-input-bordered" id="name-create-organization-text-field" required="required">
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <input type="date" v-model="instance.dueDate" class="form-control form-input form-input-bordered" id="name-create-organization-text-field" required="required">
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="instance.applicableMonth" /><span class="text-90 whitespace-nowrap label">{{instance.applicableMonth}}</span></div>
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="instance.status" /><span class="label">Not Started</span></div>
						            </td>
						    	</tr>
						    	<tr class="bg-gray-50 dark:bg-gray-800">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2" colspan=5><span>Set Timelines (in Days)</span></td>
						    	</tr>
						    	<tr class="group">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<span class="label">Reminder Before </span>
						            </td>
						            <td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<input type="text" placeholder="" class="w-full form-control form-input form-input-bordered" id="description" dusk="description" v-model="covData.reminder.before"/>
						            </td>
						    	</tr>
						    	<tr class="group">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<span class="label">Reminder Interval </span>
						            </td>
						            <td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<input type="text" placeholder="" class="w-full form-control form-input form-input-bordered" id="description" dusk="description" v-model="covData.reminder.interval"/>
						            </td>
						    	</tr>
						    </tbody>
			          </table>
			          <div v-if="typeof covData.child !== 'undefined' && Object.keys(covData.child).length>0" v-for="child in covData.child">
			          	<div class="bg-gray-50 dark:bg-gray-800">
			          		Sub Covenant - {{child.child_label}}
			          	</div>
				          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
				            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
				              <label for="" class="inline-block pt-2 leading-tight label">Start Date (Allotment Date) <span class="text-red-500 text-sm">*</span>
				              </label>
				            </div>
				            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
				            	<label for="" class="inline-block pt-2 leading-tight label">{{child.activateDate}}
			              		</label>
				            </div>
				          </div>
				          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
				            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
				              <label for="" class="inline-block pt-2 leading-tight label">End Date (Allotment Date) <span class="text-red-500 text-sm">*</span>
				              </label>
				            </div>
				            <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 w-full md:w-3/5 md:py-5">
				              <label for="" class="inline-block pt-2 leading-tight label">{{child.dueDate}}
			              		</label>
				            </div>
				          </div>
				          <table class="w-full table-default" cellpadding="0" cellspacing="0" data-testid="resource-table" >
						    <thead class="bg-gray-50 dark:bg-gray-800">
						    	<tr>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Instance#</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Activate Date </span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Due Date</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Applicable Month</span></th>
						            <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"><span>Status</span></th>
						         </tr>
						    </thead>
						    <tbody>
						    	<tr dusk="3-row" class="group">
						    		<td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="child.instanceNo" /><span class="text-90 whitespace-nowrap">{{child.instanceNo}}</span></div>
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <input type="date" v-model="child.activateDate" class="form-control form-input form-input-bordered" id="name-create-organization-text-field" required="required">
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <input type="date" v-model="child.dueDate" class="form-control form-input form-input-bordered" id="name-create-organization-text-field" required="required">
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="child.applicableMonth" /><span class="text-90 whitespace-nowrap label">{{child.applicableMonth}}</span></div>
						            </td>
						            <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
						               <div class="text-left"><input type="hidden" v-model="child.status" /><span class="label">{{child.status}}</span></div>
						            </td>
						    	</tr>
						    	<tr class="bg-gray-50 dark:bg-gray-800">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2" colspan=5><span>Set Timelines (in Days)</span></td>
						    	</tr>
						    	<tr class="group">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<span class="label">Reminder Before </span>
						            </td>
						            <td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<input type="text" placeholder="" class="w-full form-control form-input form-input-bordered" id="description" dusk="description" v-model="covData.reminder.before"/>
						            </td>
						    	</tr>
						    	<tr class="group">
						    		<td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<span class="label">Reminder Interval </span>
						            </td>
						            <td class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
						    			<input type="text" placeholder="" class="w-full form-control form-input form-input-bordered" id="description" dusk="description" v-model="covData.reminder.interval"/>
						            </td>
						    	</tr>
						    </tbody>
			          </table>
			      	  </div>
					</div>
	       		</div>
	       		<button size="lg" align="center" component="button" dusk="create-button" type="submit" class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900" style="    margin: 30px;"><span class="">Update</span><!----></button>
	    	</div>
	    </form>
	</div>
</template>
<script>
export default {
  name: 'app',
  props: ['covData'],
  data() {
    return {
 	  //'complianceId':this.complianceId,
 	  'complianceId':84,
 	  'compliance': null,
 	  'months': ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
    }
  },
  methods: {
  	saveTimelines() {
      Nova.request().post('/nova-vendor/covenants/saveTimeline',this.covData)
      .then(
          response => {
            if(response.data.status == 'success') {
                window.location.href="/admin/covenants";
              }
          }
      )
    },
  },
  created:function(){

  },


 }
</script>
<style scoped>
.title {
  cursor: pointer;
  display: flex;
  justify-content: space-between;
}
.title,
.description {
  border: 1px solid black;
  padding: 5px;
}
</style>