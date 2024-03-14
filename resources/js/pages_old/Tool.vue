<template>
  <div id="app">
    <h1 class="text-90 font-normal text-xl md:text-2xl mb-3">Compliance Break up</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow" style="padding:20px;">
      <table class="w-full table-default" id="covenant-list">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th style="width:auto;"></th>
            <th style="width:auto;">SN</th>
            <th style="width:auto;">CMP Id</th>
            <th style="width:auto;">Covenant Type</th>
            <th style="width:auto;">Sub type</th>
            <th style="width:auto;">Description</th>
            <th style="width:auto;">Frequency</th>
            <th style="width:auto;">Start Date</th>
            <th style="width:auto;">Due Date</th>
            <th style="width:auto;">Status</th>
            <th style="width:auto;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(covenant,index) in covenants" :key="covenant.covenant_id" :id="covenant.covenant_id">
            <td>             
              <input type="checkbox" :value="covenant.covenant_id" :id="covenant.covenant_id" name="checkApproval[]" v-if="(viewOnly != 1 && covenant.covenantStatus == 'Draft') || (isApprover == 1 && covenant.covenantStatus == 'Pending For Approval')"/>
            </td>
            <td class="px-2 py-2 border-t border-gray-100"></td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.compliance_id}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.type}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.subType}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.description}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.frequency}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.startDate}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.dueDate}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.covenantStatus}}</td>
            <td class="actions-icons">

            <div class="dropdown">
              <span class="three-dots"></span>
              <div class="dropdown-content">
                <ul style="padding: 0px;">
                  <li>
                    <span style="width: 100%; display: flex; padding-top: 8px;">
                      <Link href="/admin/covenants/edit" method="get" aria-label="Edit" dusk="3-edit-button" class="toolbar-button hover:text-primary-500 px-2 v-popper--has-tooltip point" :data="{ id: covenant.covenant_id }" v-if="viewOnly != 1 && (covenant.covenantStatus == 'Draft' || covenant.covenantStatus == 'Rejected')" title="Edit">Edit</Link>
                    </span>
                  </li>
                  <li><span style="padding: 8px;" class="point"><a @click.prevent="view(covenant.covenant_id)" title="View">View</a></span></li>
                  <li>
                    <span style="width: 100%; display: flex; padding-top: 8px;">
                      <Link href="/admin/covenants/clone" method="get" aria-label="Clone" title="Clone" dusk="3-edit-button" class="toolbar-button hover:text-primary-500 px-2 v-popper--has-tooltip" :data="{ id: covenant.covenant_id }" v-if="viewOnly != 1">Clone</Link>
                    </span>
                  </li>
                  <li>
                    <span style="width: 100%; display: flex;">
                      <Link href="/admin/covenants/timeline" method="get" aria-label="Timeline" title="Timeline" dusk="3-edit-button" class="toolbar-button hover:text-primary-500 px-2 v-popper--has-tooltip" :data="{ id: covenant.covenant_id }" v-if="viewOnly != 1">Timeline</Link>
                    </span>
                  </li>
                  <li>
                    <span style="width: 100%; display: flex; padding-top: 8px;">
                      <div v-if="isApprover == 1" class="point">
                        <a @click.prevent="mark('Approved',covenant.covenant_id)" title="Approve" v-if="covenant.covenantStatus == 'Pending For Approval'"> Approve</a>
                        <a @click.prevent="mark('Rejected',covenant.covenant_id)" title="Reject" v-if="covenant.covenantStatus == 'Pending For Approval'"> Reject</a>
                      </div>
                      <a class="point" @click.prevent="submitForApproval(covenant.covenant_id)" title="Submit For Approval" v-if="viewOnly != 1 && (covenant.covenantStatus == 'Draft' || covenant.covenantStatus == 'Rejected')" > Submit For Approval</a>
                    </span>
                  </li>
                </ul>
              </div>
            </div>

             
              
            </td>
          </tr>           
        </tbody>
      </table>
      <div style="padding-top:30px;" v-if="isApprover == 1"><button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-2 h-9 text-sm flex-shrink-0" @click.prevent="multiMark('Approved')">Approve</button> <button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-2 h-9 text-sm flex-shrink-0" @click.prevent="multiMark('Rejected')">Reject</button>
      </div>
      <div style="padding-top:30px;" v-else><button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-2 h-9 text-sm flex-shrink-0" @click.prevent="multiSubmitForApproval()">Submit For Approval</button>
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
  methods: {
      closeModal() {
        this.isModalVisible = false;
      },
      getCovenants() {
        Nova.request().post('/nova-vendor/covenants/list',{'id':getParam()})
        .then(response => {
            this.covenants = response.data.compliance_covenants;
            this.viewOnly = response.data.viewOnly;
            this.isApprover = response.data.isApprover;
            setTimeout(() => {
              var table = $('#covenant-list').DataTable({
                  columns: [
                      { data: 'covenant_id',"width": "5%"},
                      {
                        data: ''
                      },
                      { data: 'compliance_id',"width": "5%" },
                      { data: 'type',"width": "5%" },
                      { data: 'subType',"width": "15%" },
                      { data: 'description',"width": "15%" },
                      { data: 'frequency',"width": "6%" },
                      { data: 'startDate',"width": "12%" },
                      { data: 'dueDate',"width": "12%" },
                      { data: 'covenantStatus',"width": "15%" },
                      { title: "Actions", "defaultContent": "<button onclick='edititem();'>Edit</button>","width": "1%"},
                    ],
                    
                    "columnDefs": [
                        { "targets": [2,3,4,5,6,7], "searchable": true }
                    ],
                    order: [],
                    //stateSave: true,
                    

                    
              });
              table.on('order.dt search.dt', function () {
                      let i = 1;
               
                      table.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
                          this.data(i++);
                      });
                  }).draw();
            },100);
        });
      },

      view(id) {
        Nova.request().post('/nova-vendor/covenants/view',{'id':id})
        .then(response => {
            if(response.data.status == 'success') {
              this.viewCovenant = response.data.covenant;
              this.isModalVisible = true;
            }            
        });
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
thead {
    position: sticky;
    top: 0px;
  }
</style>
