<template>
  <div id="app">
    <h1 class="text-90 font-normal text-xl md:text-2xl mb-3">Compliance Break up</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow" style="padding:20px;">
      <table ref="dataTable" class="w-full table-default" id="covenant-list">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th style="width:auto;"></th>
            <th style="width:auto;">CMP Id</th>
            <th style="width:auto;">Covenant Type</th>
            <th style="width:auto;">Sub type</th>
            <th style="width:auto;">Description</th>
            <th style="width:auto;">Frequency</th>
            <th style="width:auto;">Start Date</th>
            <th style="width:auto;">Due Date</th>
            <th style="width:auto;">Status</th>
            <th style="width:auto;">Maker</th>
            <th style="width:auto;">Actions</th>
          </tr>
        </thead>
      </table>
      <div style="padding-top:30px;" class="point" v-if="isApprover == 1"><button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-2 h-9 text-sm flex-shrink-0" @click.prevent="multiMark('Approved')">Approve</button> <button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-2 h-9 text-sm flex-shrink-0" @click.prevent="multiMark('Rejected')">Reject</button>
      </div>
    </div>
      <div id="viewModal">
        <Modal
      v-show="isModalVisible"
      @close="closeModal"
      :covenant="viewCovenant"
    />
    </div>

        <div>
      <Mark
          v-show="isMarkVisible"
          @close="closeMark"
          :selectedData="selectedData" 
          :status="approvalStatus"
        />
    </div>
  </div>
</template>

<script>
function getParam()
  {
      return window.location.href.slice(window.location.href.indexOf('?') + 1).split('=')[1];
  } 
import { Link } from '@inertiajs/inertia-vue3';
import "datatables.net-dt/js/dataTables.dataTables";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import $ from 'jquery';
import Modal from '../../../../../nova-components/Covenants/resources/js/pages/View.vue';
import Mark from '../../../../../nova-components/Covenants/resources/js/pages/Mark.vue';
export default {
  data() {
    return {
      covenants:[],
      isModalVisible: false,
      viewCovenant: {},
      viewOnly: 1,
      isApprover: 0,
      isMarkVisible: false,
      selectedData: [],
      approvalStatus: '',
    }
  },
  components: {
    Modal,
    Mark
  },
  props: ['viewOnly','isApprover'],
  methods: {
      closeModal() {
        this.isModalVisible = false;
      },
      getCovenants() {

        setTimeout(() => {
          const self = this;
          var table = $(this.$refs.dataTable).DataTable({
            "serverSide": true,
            "processing": true,
            "ajax":{
              "url": "/nova-vendor/covenants/pending-approval-list",
              "dataType": "json",
              "type": "POST",
              "data": {_token:$('meta[name="csrf-token"]').attr('content')}
              },
              language: {
                  searchPlaceholder: "Search by CMP Id"
              },
              columns: [
                  { data: 'checkbox',"width": "5%", 'orderable':false},
                  { data: 'compliance_id',"width": "5%", 'orderable':false },
                  { data: 'type',"width": "5%",'orderable':false  },
                  { data: 'subType',"width": "15%",'orderable':false  },
                  { data: 'description',"width": "15%",'orderable':false  },
                  { data: 'frequency',"width": "6%" , 'orderable':false },
                  { data: 'startDate',"width": "12%", 'orderable':false },
                  { data: 'dueDate',"width": "12%", 'orderable':false },
                  { data: 'covenantStatus',"width": "15%", 'orderable':false },
                  { data: 'name',"width": "15%",'orderable':false },
                  { data: 'actions', 'orderable':false
                    },
                ],
                
                "columnDefs": [
                    { "targets": [2,3,4,5,6,7], "searchable": true }
                ],
                //order: [[1, 'asc']],
                stateSave: false,
                "deferRender": true,
                  rowCallback(row, data) {
                    $(row).on('click', '.view-placeholder',() => {
                      console.log('===================', JSON.stringify(data));
                      self.view(data.covenant_id);
                    });
                    $(row).on('click', '.timeline-placeholder',() => {
                      self.timeline(data.covenant_id);
                    });
                    $(row).on('click', '.approve-placeholder',() => {
                      self.mark('Approved',data.covenant_id);
                    });
                    $(row).on('click', '.reject-placeholder',() => {
                      self.mark('Rejected',data.covenant_id);
                    });
                  },

          });

      },100);

        /*Nova.request().get('/nova-vendor/covenants/pending-approval-list')
        .then(response => {
            this.covenants = response.data.compliance_covenants;
            this.viewOnly = response.data.viewOnly;
            this.isApprover = response.data.isApprover;
            
        });*/
      },

      view(id) {
        console.log('Logging ID:', id);
        Nova.request().post('/nova-vendor/covenants/view',{'id':id})
        .then(response => {
            if(response.data.status == 'success') {
              this.viewCovenant = response.data.covenant;
              this.isModalVisible = true;
            }            
        });
      },

      timeline(id) {
        var url = "/admin/covenants/timeline/"+id;
        window.open(url, '_blank');
      },

      submitForApproval(id) {
        var ids = [id];
        Nova.request().post('/nova-vendor/covenants/submitForApproval',{'id':ids})
        .then(response => {
            if(response.data.success) {
              location.reload();
            }
        });
      },

      multiSubmitForApproval() {
        var ids = [];
        $("input[type='checkbox'][name='checkApproval[]']:checked").each(function () {
              ids.push($(this).val());
        });
        Nova.request().post('/nova-vendor/covenants/submitForApproval',{'id':ids})
        .then(response => {
            if(response.data.success) {
              location.reload();
            }
        });
      },

      mark(status,id) {
          this.selectedData = [id];
          this.approvalStatus = status;
          this.isMarkVisible = true;  
      },

      multiMark(status) {
          var temp = [];
          $("input[type='checkbox'][name='checkApproval[]']:checked").each(function () {
              temp.push($(this).val());
          });
          this.selectedData = temp;
          this.approvalStatus = status;
          this.isMarkVisible = true;  
      },

      closeMark() {
          this.isMarkVisible = false;
      },
  },
  created:function(){
   this.getCovenants();
  },
  mounted() {
    //this.getCompliance();
  },
}
</script>

<style>
.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {

  display: none;
  position: absolute;
  background-color: #f9f9f9;
  width: auto;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  padding: 15px;
  z-index: 1;
}

a {
  color: #96144c !important;
  text-decoration: none
}

.dropdown:hover .dropdown-content {
  display: block;
}

  .dot{
    height: 4px;
    width: 4px;
    background-color: #999;
    border-radius: 50%;
    display: inline-block;
    font-weight: bold;
    margin: 1px;
  }
  .three-dots:after {
  cursor: pointer;
  color: #888;
  content: '\2807';
  font-size: 20px;
  padding: 0 5px;
}
thead {
    position: sticky;
    top: 0px;
  }
</style>
