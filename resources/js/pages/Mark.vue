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
              <h1 class="text-90 font-normal text-xl md:text-2xl mb-3">{{status}} Covenant</h1>
              <div class="event-info-wrapper">
        	<div class="event-info-content">
            <Card>
            <form id="compliance_form" enctype="multipart/form-data" @submit.prevent="handleSubmit()">
            <div class="info-container upload-controller">
              <div class="info-item">
                <h4>Status:</h4>
                <label class="label">{{status}}</label>
              </div>              
            </div>

			<div class="info-item1 message">{{this.message}}</div>

            <div class="text-area-container">
              <textarea v-model="comment" maxlength="300" placeholder="Add Comments" v-if="status == 'Approved'">
              </textarea>
              <textarea v-model="comment" maxlength="300" placeholder="Add Comments" v-else-if="status == 'Rejected'" required="required">
              </textarea>
            </div>

            <div class="button-group">
              <button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0" type="submit">Submit</button>        
            </div>
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
    props: ['selectedData','status'],
    data() {
    return {
      comment:'',
      message: '',
    }
  },
    methods: {
      close() {
        this.$emit('close');
      },
      handleSubmit(){
	      //let formData = new FormData();
	      //formData.append('selectedData', this.selectedData);
	      //formData.append('status', this.status);
	      console.log(this.selectedData);
	      var formData = {};
	      formData.selectedData = this.selectedData;
	      formData.status = this.status;
	      formData.comment = this.comment;
	      Nova.request().post('/nova-vendor/covenants/approve',formData)
        .then(
            response => {
              if(response.data.success) {
                    this.message = "Data is updated.";
                    location.reload();
                    this.$emit('close');
              }

            }
        ).catch(err => {          
          if(err.response && err.response.data &&  err.response.data.errors){
            this.errors = err.response.data.errors;
            window.scrollTo(0,0);
          }
        
        });
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