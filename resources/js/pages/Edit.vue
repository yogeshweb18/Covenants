<template>
	<div id="app">
      <h1 v-if="action == 'clone'" class="text-90 font-normal text-xl md:text-2xl mb-3">Clone Covenant</h1>
      <h1 v-else class="text-90 font-normal text-xl md:text-2xl mb-3">Edit Covenant</h1>
      <form id="compliance_form" class="dark:text-white text-lg opacity-70" enctype="multipart/form-data" style="min-height: 300px" @submit.prevent="saveData()">
      <div class="mb-8 space-y-4"></div>
      
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Type <span class="text-red-500 text-sm"></span>
              </label>
            </div>
            <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{covenant.type}}</label>                
            </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Sub Type <span class="text-red-500 text-sm"></span>
              </label>
            </div>
            <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
                <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{covenant.subType}}</label>                
            </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block pt-2 leading-tight">Description <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="text" placeholder="Document Name" v-model="covenant.description" class="w-full form-control form-input form-input-bordered" id="name-create-docName-text-field" name="docName" dusk="docName" list="name-list" required="required" pattern='^[a-zA-Z0-9.,\/\" \-]+$'><!----><!----><!---->
            </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block pt-2 leading-tight">Comments
              </label>
            </div>
            <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="text" v-model="covenant.comments" class="w-full form-control form-input form-input-bordered" id="name-create-docName-text-field" name="docName" dusk="docName" list="name-list" pattern='^[a-zA-Z0-9.,\/\" \-]+$'><!----><!----><!---->
            </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
          <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
            <label for="" class="inline-block pt-2 leading-tight">Frequency <span class="text-red-500 text-sm">*</span>
            </label>
          </div>
          <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
            <div class="text-left">
                      <select class="w-full form-control form-input-bordered select-box" v-model="covenant.frequency" required>
                        <option value="Monthly">Monthly</option>
                        <option value="Quarterly">Quarterly</option>
                        <option value="Half Yearly">Half Yearly</option>
                        <option value="Annually">Annually</option>
                        <option value="One Time">One Time</option>
                        <option value="Event Based">Event Based</option>
                    </select>
                   </div>
          </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block pt-2 leading-tight">Start Date <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="date" v-model="covenant.startDate" class="date-field form-input form-input-bordered" id="name-create-organization-text-field" name="startDate" dusk="startDate" required="required"><!----><!----><!---->
            </div>
          </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block pt-2 leading-tight">Due Date <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="date" v-model="covenant.dueDate" class="date-field form-input form-input-bordered" id="name-create-endDate-text-field" name="endDate" dusk="endDate" required="required"><!----><!----><!---->
            </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block pt-2 leading-tight">Applicable Month <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <select class="form-input-bordered select-box form-control" v-model="covenant.applicableMonth" required>
                    <option v-for="data in this.months" :value="data">{{data}}</option>
                </select>
            </div>
          </div>
          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label for="" class="inline-block pt-2 leading-tight">Target Value <span class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
              <input type="text" v-model="covenant.targetValue" class="w-full form-control form-input form-input-bordered" id="name-create-inconsistencyTreatment-text-field" required="required"><!----><!----><!---->
            </div>
          </div>


          <div v-if="covenant.isCustomCovenant == 0">
          <div v-if="typeof covenant.covenantParameters !== 'undefined' && Object.keys(covenant.covenantParameters).length>0" v-for="param in covenant.covenantParameters">
          	<input type="hidden" name="paramKey[]" class="w-full form-control form-input form-input-bordered" id="name-create-paramKey-text-field" dusk="paramKey" list="name-list" disabled v-model="param.key" />
          	<div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" v-if="typeof param.type !== 'undefined' && param.type=='dropdown'"> 
              <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5"> 
          	     <label for="" class="inline-block pt-2 leading-tight">{{param.label}} <span class="text-red-500 text-sm">*</span>
                </label>
              </div>
              <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
       		       <select class="w-full form-input-bordered select-box" v-model="param.value">
                  <option v-for="data in param.option" :value="data">{{data}}</option>
                </select>
              </div>
        	 </div>
       		<div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" v-if="typeof param.type !== 'undefined' && param.type=='text'">
       			<input type="hidden" name="paramKey[]" id="name-create-paramKey-text-field" dusk="paramKey" list="name-list" disabled v-model="param.key" />
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
              <label>{{param.label}}</label> 
            </div>
            <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
       			  <input type="text" placeholder="" name="paramValue[]" class="form-control form-input form-input-bordered" id="name-create-paramValue-text-field" dusk="frequency" list="name-list" v-model="param.value" />
            </div>
       		</div>
       		<div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" v-if="typeof param.type !== 'undefined' && param.type=='optional'">
       			<input type="hidden" name="paramKey[]" id="name-create-paramKey-text-field" dusk="paramKey" list="name-list" disabled v-model="param.key" />
            <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">
            <label>{{param.label}}</label> 
            </div>
            <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
       			<input type="text" placeholder="" name="paramValue[]" class="form-control form-input form-input-bordered" id="name-create-paramValue-text-field" dusk="frequency" list="name-list" v-model="param.value" />
            </div>
       		</div>
       	  </div>

          <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" v-if="typeof covenant.child !== 'undefined' && covenant.child.length>0" v-for="child in covenant.child">
          <div class="px-6 md:px-8 mt-2 md:mt-0 w-full md:w-1/5 md:py-5">  
     			<label>{{child.label}} </label>
          </div>
          <div class="mt-1 md:mt-0 px-6 md:px-8 w-full md:w-3/5 md:py-5">
     			<input type="date" placeholder="" name="child_covenant_value" class="form-control form-input form-input-bordered" id="child_covenant_value" dusk="child_covenant_value" v-model="child.value" />
          </div>
     		  </div>

     		</div>
   		<div v-if="covenant.isCustomCovenant == 1">
          <div v-if="typeof covenant.custom_parameter !== 'undefined'">
       		<div>
       			<label>Parameter</label> :
       			<input type="text" placeholder="" class="form-control form-input form-input-bordered" id="name-create-paramValue-text-field" dusk="frequency" list="name-list" v-model="covenant.custom_parameter" />
       		</div>
       		<div>
       			<label>Parameter Details</label> :
       			<input type="text" placeholder="" class="form-control form-input form-input-bordered" id="name-create-paramValue-text-field" dusk="frequency" list="name-list" v-model="covenant.custom_value" />
       		</div>
       	  </div>
          <div v-if="typeof covenant.custom_child !== 'undefined'">
       		<div>
       			<label>Child Covenant</label> :
       			<input type="text" placeholder="" class="form-control form-input form-input-bordered" id="name-create-paramValue-text-field" dusk="frequency" list="name-list" v-model="covenant.custom_child" />
       		</div>
       		<div>
       			<label>Child Due Date</label> :
       			<input type="text" placeholder="" class="form-control form-input form-input-bordered" id="name-create-paramValue-text-field" dusk="frequency" list="name-list" v-model="covenant.custom_child_dueDate" />
       		</div>
       	  </div>
   		</div>
          <div v-if="action == 'clone'">
            <button size="lg" align="center" component="button" dusk="create-button" type="submit" class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900" style="margin: 30px;"><span class="">Add</span><!----></button>
          </div>
          <div v-else>
            <button size="lg" align="center" component="button" dusk="create-button" type="submit" class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900" style="    margin: 30px;"><span class="">Update</span><!----></button>
          </div>
      </div>  
    </form>
</div>
</template>
<script>
export default {
  name: 'app',
  props: {
	    covenant: {},
      action:'',
	},
  data() {
    return {
      'months': ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
   }
  },
  methods: {
    saveData() {
      if(this.action == 'clone') {
        var url = '/nova-vendor/covenants/addClone';
      } else {
        var url = '/nova-vendor/covenants/update';
      }
    	Nova.request().post(url,this.covenant)
	      .then(
	          response => {
              if(response.data.status == 'success') {
                window.location.href="/admin/covenants?id="+response.data.complianceId;
              }
	          }
	      )
    }
  },
  created:function(){

   },
}
</script>