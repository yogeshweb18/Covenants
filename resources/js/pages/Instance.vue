<template>
  <div class="modal-backdrop">
      <div class="modal30">
          <header class="">
            <button
              type="button"
              class="btn-close"
              @click="close"
            >
              x
            </button>
          </header>
          <h1 class="modal-header1 text-90 font-normal text-xl md:text-2xl mb-3">View Covenant</h1>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Type</label>
                      <span class="answer">
                       <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.type}}</label>
                      </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Sub Type <span class="text-red-500 text-sm">*</span></label>
                       <span class="answer">
                         <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.subType}}</label>
                       </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Description <span class="text-red-500 text-sm">*</span></label>
                      <span class="answer">
                        <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.description}}</label>               
                      </span>
                  </div>
                </div>
              </div> 
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Comments <span class="text-red-500 text-sm">*</span></label>
                       <span class="answer">
                        <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.comments}}</label>
                      </span>
                  </div>
                </div>
              </div>  
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Upload <span class="text-red-500 text-sm">*</span></label>
                       <span class="answer">
                        <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.upload}}</label>
                      </span>
                  </div>
                </div>
              </div> 
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                    <div class="mt-2 md:mt-0">
                        <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Upload <span class="text-red-500 text-sm">*</span></label>
                        <span class="answer">
                          <a href="{{ route('download.file', ['filename' => $covenantDetails['upload']]) }}">Download File</a>

                          <a href="{{ route('download.file', ['filename' => instance.upload]) }}">Download File</a>
                          
                          <a href="/download/{{ instance.upload }}">Download File</a>
                        </span>
                    </div>
                </div>
            </div>

              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                 <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Frequency <span class="text-red-500 text-sm">*</span></label>
                       <span class="answer">
                         <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.frequency}}</label>               
                       </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Target Value <span class="text-red-500 text-sm">*</span></label>
                     <span class="answer">
                      <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.targetValue}}</label>               
                     </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Start Date <span class="text-red-500 text-sm">*</span></label>
                    <span class="answer">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.startDate}}</label>
                    </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Due Date <span class="text-red-500 text-sm">*</span></label>
                      <span class="answer">
                        <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.dueDate}}</label>
                      </span>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800">
                <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row" index="0">
                  <div class="mt-2 md:mt-0">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">Applicable Month</label>
                    <span class="answer">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.applicableMonth}}</label>           
                    </span>
                  </div>
                 </div>
              </div>
              <div v-if="instance.isCustomCovenant == 0">
                <div v-if="typeof instance.covenantParameters !== 'undefined' && Object.keys(instance.covenantParameters).length>0" v-for="param in instance.covenantParameters">
                  <div class="bg-white dark:bg-gray-800">
                    <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row">
                      <div class="mt-2 md:mt-0">
                      <label for="" class="inline-block pt-2 leading-tight">{{param.label}}
                      </label>
                       <span class="answer">
                       <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{param.value}}</label>      
                       </span>
                      </div>
                     </div>
                  </div>
                </div>
                <div v-if="typeof instance.child !== 'undefined' && instance.child.length>0" v-for="child in instance.child">
                  <div class="bg-white dark:bg-gray-800">
                    <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row">
                      <div class="mt-2 md:mt-0">
                      <label for="" class="inline-block pt-2 leading-tight">{{child.label}}
                      </label>                   
                      <span class="answer">
                      <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{child.value}}</label>     
                      </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div> 

              <div v-if="instance.isCustomCovenant == 1">
                <div v-if="typeof instance.custom_parameter !== 'undefined'">
                 <div class="bg-white dark:bg-gray-800">
                  <div class="field-wrapper flex flex-col border-b border-gray-100 dark:border-gray-700 md:flex-row">
                    <div class="mt-2 md:mt-0">
                    <label for="" class="inline-block pt-2 leading-tight">Parameter</label>
                     <span class="answer">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.custom_parameter}}</label>
                    </span>
                    </div>
                  </div>
                  <div class="mt-2 md:mt-0">
                    <label for="" class="inline-block pt-2 leading-tight">Value</label>
                    <span class="answer">
                    <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.custom_value}}</label>
                  </span>
                  </div>
                </div>
                </div>
                <div v-if="typeof instance.custom_child !== 'undefined'">
                  <div class="bg-white dark:bg-gray-800">
                    <div class="mt-2 md:mt-0">
                      <label for="" class="inline-block pt-2 leading-tight">Child Covenant</label>
                      <span class="answer">
                      <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.custom_child}}</label>
                    </span>
                    </div>
                    <div class="mt-2 md:mt-0">
                      <label for="" class="inline-block pt-2 leading-tight">Child Due Date</label>
                      <span class="answer">
                      <label for="name-create-organization-text-field" class="inline-block pt-2 leading-tight">{{instance.custom_child_dueDate}}</label>
                    </span>
                    </div>
                  </div>
                </div>
            </div>
              <footer class="modal-footer">
        <button
          type="button"
          class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0"
          @click="close"
        >
          Close
        </button>
      </footer>            
        </div>  
        
    </div>
</template>

<script>
  export default {
    name: 'Modalview',
    props: ['instance'],
    methods: {
      close() {
        this.$emit('close');
      },
    },
  };
</script>

<style>
  .modal-backdrop {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .modal {
    background: #FFFFFF;
    box-shadow: 2px 2px 20px 1px;
    overflow-x: auto;
    display: flex;
    flex-direction: column;
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