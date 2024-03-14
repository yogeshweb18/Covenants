<template>
  <div class="modal-backdrop">
      <div class="modal30">
          <header class="modal-header">
            <button
              type="button"
              class="btn-close"
              @click="close"
            >
              x
            </button>
          </header>
            <div class="mb-8 space-y-4"></div>
              <h1 class="text-90 font-normal text-xl md:text-2xl mb-3">Resolve Covenant</h1>
              <div class="event-info-wrapper">
        <div class="event-info-content">
            <div v-if="selectedData.status == 3">
              <button class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3"><Link class="white" size="md" href="/admin/compliance-overview/add-tracking" method="post" :data="{complianceId:selectedData.complianceId,covenantId:selectedData.covenantId,action:'add-tracking'}">Add More Tracking</Link></button>
            </div>

            <Card v-if="selectedData.status != 2  && selectedData.status != 3">
            <form id="compliance_form" enctype="multipart/form-data" @submit.prevent="handleSubmit()">
            <div class="info-container upload-controller">
              <div class="info-item">
                <h4>Resolution:</h4>
                <input name="resolution" v-model="resolution" @blur.prevent="notifyIfFail()" placeholder="Enter Value" required="required" />
              </div>              
              <div class="info-item">
                <button @click.prevent="handleUploadFileClick" class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3">Upload File</button>
                <input type="file" id="file" name="file" style="display: none;" ref="file" multiple="multiple" @change="handleSelectedFilesChange">
              </div>
            </div>

			<div class="info-item1 error-message">{{this.failureMessage}}</div>
            
            <div class="selected-file-list">
              <p>{{ selectedFiles.length }} files selected</p>
              <div class="selected-files-container">
                <div v-for="f, index in selectedFiles" :key="index">
                  {{ f.name }}
                  <img src="/img/cross1.png" alt="cross" @click="handleRemoveSelectedFile(index)">
                </div>
              </div>
            </div>

            <div class="text-area-container">
              <textarea v-model="comments" maxlength="300" placeholder="Add Comments">
              </textarea>
            </div>
            
            <div class="info-result">
              <h4>Result:</h4>
              <div
                class="radio-button" id="pass"
                :class="{ active: infoResult === 'pass' }"
                @click="infoResult = 'pass'"
              >Pass</div>
              <div
                class="radio-button" id="fail" 
                :class="{ active: infoResult === 'fail' }"
                @click="infoResult = 'fail'"
              >Fail</div>
              <!-- <input type="checkbox" name="" id="" v-if="infoResult=='fail'"> <span>Notify Customer</span> -->
              <div
                class="notify-check"
                :class="{active: notifyCheck}"
                v-if="infoResult === 'fail'"
                @click="notifyCheck = !notifyCheck"
              ><img src="/img/check.png" alt="check" /></div>
              <span v-if="infoResult === 'fail'">Notify Customer</span>
            </div>
            <div class="button-group">
              <button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0" type="submit" v-if="hide_submit == 0">Submit</button>        
              <Link size="md" href="/admin/compliance-overview/add-tracking" method="post" :data="{complianceId:selectedData.complianceId,covenantId:selectedData.covenantId,action:'add-tracking'}" v-if="tracking_on == 1"><button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0">Add More Tracking</button></Link>
            </div>
            </form>       

            <div class="info-container" v-if="failed_covenant.type == 'covenant'">

              <Link size="md" href="/admin/compliance-overview/add-tracking" method="post" :data="{complianceId:selectedData.complianceId,covenantId:selectedData.covenantId,action:'trigger-covenant',data:failed_covenant.key}"><button>Apply {{failed_covenant.label}}</button></Link>

            </div>
            <form :action="form.action" class="formaction" v-else-if="is_defined_failed == 1">
              <div class="info-container" v-if="failed_covenant.type == 'date'">
                <label class="label">{{failed_covenant.label}} :</label>
                <input type="date" class="form-control form-input form-input-bordered" v-model="failed_covenant.value" />
              </div>
              <div class="line-height3" v-for="param in failed_covenant.parameters">
                <select class="w-full form-input-bordered select-box" v-model="param.value" 
                  v-if="param.type=='dropdown'" required="required">
                    <option value="" selected>{{param.label}}</option>
                    <option v-for="data in param.option" :value="data">{{data}}</option>
                </select>
                  <div class="line-height3" v-if="param.type=='text'">
                    <label class="label">{{param.label}}</label> :
                    <input type="text" placeholder="" name="paramValue[]" class="field form-control form-input form-input-bordered" id="name-create-paramValue-text-field" dusk="frequency" v-model="param.value" required="required"/>
                  </div>
                  <div class="line-height3" v-if="param.type=='date'">
                    <label class="label">{{param.label}}</label> :
                    <input type="date" placeholder="" name="paramValue[]" class="field form-control form-input form-input-bordered" id="name-create-paramValue-text-field" dusk="frequency" v-model="param.value" required="required"/>
                  </div>
                  <div class="line-height3" v-if="param.type=='optional'">
                    <label class="label">{{param.label}}</label> :
                    <input type="text" placeholder="" name="paramValue[]" class="field form-control form-input form-input-bordered" id="name-create-paramValue-text-field" dusk="frequency"  v-model="param.value" required="required"/>
                  </div>
              </div>
              <button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0" @click.prevent="saveFailCovenant">Submit</button>
          </form>
        </Card>
        </div>
      </div>
      <div class="spinner" v-if="uploading">
        <img src="/img/spinner.gif" alt="spinner" />
      </div>        
              <!--<footer class="modal-footer">
        <button
          type="button"
          class="btn-green"
          @click="close"
        >
          Close
        </button>
      </footer>      -->      
        </div>  
        
    </div>
</template>

<script>
  export default {
    name: 'Modal',
    props: ['selectedData'],
    data() {
    return {
      tracking_on : 0,
      resolution: '',
      commets: '',
      compliance: {},
      uploading: false,
      failureMessage: '',
      selectedFiles: [],
      infoResult: 'pass',
      notifyCheck: false,
      is_defined_failed: 0,
      failed_covenant: [],
      hide_submit: 0,
      form:{
        action: '',
      }
    }
  },
    methods: {
      close() {
        this.$emit('close');
      },
      handleUploadFileClick() {
	      this.$refs.file.value = null
	      this.$refs.file.click()
	    },

	    handleSelectedFilesChange() {
	      this.selectedFiles = []
	      for(let i = 0; i < this.$refs.file.files.length; i ++) {
	        this.selectedFiles.push(this.$refs.file.files[i])
	      }
	    },
	    
	    handleRemoveSelectedFile(index) {
	      let tmp = []
	      
	      this.selectedFiles.forEach((file, i) => {
	        if(i !== index) {
	          tmp.push(file)
	        }
	      })
	      this.selectedFiles = tmp;
	    },

	    handleSubmit() {
		      if(this.resolution === '') {  
		        return; 
		      }
		      let formData = new FormData()
		      formData.append('resolutionStatus', this.infoResult)
		      formData.append('status', this.selectedData.status)
		      formData.append('resolution', this.resolution)
		      formData.append('comments', this.comments)
		      formData.append('instanceId', this.selectedData.instanceId)
		      formData.append('covenantId', this.selectedData.covenantId)
		      formData.append('is_child', this.selectedData.is_child)
		      formData.append('is_fail', this.selectedData.is_fail)
		      formData.append('dueDate', this.selectedData.dueDate)
		      formData.append('type', this.selectedData.type)
		      formData.append('subType', this.selectedData.subType)
		      formData.append('notifyCheck', this.notifyCheck)  
		      formData.append('mailCC', this.selectedData.mailCC)

		      for (let i = 0; i < this.selectedFiles.length; i++) {
		        formData.append('files[]', this.selectedFiles[i])
		      }

		      this.uploading = true

		      Nova.request().post('/nova-vendor/calendar/submitresult', formData).then((response) => {
		        this.uploading = false
		        if(response.data.status) {
		          var result = response.data;
		          this.tracking_on = result.tracking_on;
		          if(result.is_defined_failed == 1) {
		            this.is_defined_failed = result.is_defined_failed;
		            this.failed_covenant = result.failed_covenant;
		          }
		          else if(result.tracking_on == 0) {
		          	this.close();
                location.reload();
		          }
		          this.hide_submit = 1;
		        }
		        console.log(response.data.result);
		      })
		    },

		    saveFailCovenant(){
		      console.log(this.selectedData);

		     	var data = {
			        'complianceId' : this.selectedData.complianceId,
			        'status' : this.selectedData.status,
			        'instanceId' : this.selectedData.id,
			        'covenantId' : this.selectedData.covenantId,
			        'reminderBefore' : this.selectedData.reminderBefore,
			        'reminderInterval' : this.selectedData.reminderInterval,
			        'dueDate' : this.selectedData.dueDate,
			        'failed_covenant' : this.failed_covenant
			      }

		      Nova.request().post('/nova-vendor/calendar/saveFailCovenant', data).then((response) => {
		          console.log(response.data);
		          this.uploading = false
		          if(response.data) {
		          	this.close();
		          }
		        });
		    },

	    notifyIfFail() {
	      if(this.resolution !='' && this.selectedData.type == 'Financial') { 
	        this.uploading = true;
	        Nova.request().post('/nova-vendor/calendar/notifyIfFail', {'resolution':this.resolution,'covenantId':this.selectedData.covenantId}).then((response) => {
	          this.uploading = false;
	          this.failureMessage = '';
	          if(!response.data) {
	            this.failureMessage = "This Covenant is failed.";
	            document.getElementById("fail").classList.add('active');
	            document.getElementById("pass").classList.remove('active');
	            this.infoResult = "fail";
	          }
	        });
	      }
	    },
    },
  };
</script>

<style>
  .modal-backdrop {
    top: 20;
    bottom: 20;
    left: 20;
    right: 20;
    background-color: rgba(0, 0, 0, 0.3);
    display: block;
    float: right !important;
  }



.button-group{
	padding: 0px 20px 20px 20px;
}

 .modal30{
    width: 30% !important;
    background: #fff !important;
    display: block !important;
    float: right !important;
    padding: 20px !important;
    position: relative !important;
    z-index: 1055 !important;
    height: 100% !important;
}
  .modal-header,
  .modal-footer {
    padding: 15px;
    display: flex;
  }

  .modal-header {
    position: relative;
    border-bottom: 1px solid #eeeeee;
    color: #4AAE9B;
    justify-content: space-between;
  }

  .modal-footer {
    border-top: 1px solid #eeeeee;
    flex-direction: column;
    justify-content: flex-end;
  }

  .modal-body {
    position: relative;
    padding: 20px 10px;
  }

  .btn-close {
    position: absolute;
    top: 0;
    right: 0;
    border: none;
    font-size: 20px;
    padding: 10px;
    cursor: pointer;
    font-weight: bold;
    color: #4AAE9B;
    background: transparent;
  }

  .btn-green {
    color: white;
    background: #4AAE9B;
    border: 1px solid #4AAE9B;
    border-radius: 2px;
  }
</style>