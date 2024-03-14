<template>
  <div id="app">
    <h1 class="text-90 font-normal text-xl md:text-2xl mb-3">Approved Covenant List</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow" style="padding:20px;">
      <table class="w-full table-default" id="covenant-list">
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
        <tbody>
          <tr v-for="(covenant,index) in covenants" :key="covenant.covenant_id" :id="covenant.covenant_id">
            <td>             
              <input type="checkbox" :value="covenant.covenant_id" :id="covenant.covenant_id" name="checkApproval[]" v-if="(viewOnly != 1 && covenant.covenantStatus == 'Draft') || (isApprover == 1 && covenant.covenantStatus == 'Pending For Approval')"/>
            </td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.compliance_id}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.type}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.subType}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.description}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.frequency}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.startDate}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.dueDate}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.covenantStatus}}</td>
            <td class="px-2 py-2 border-t border-gray-100">{{covenant.name}}</td>
            <td class="actions-icons">
              <div class="dropdown">
              <span class="three-dots"></span>
              <div class="dropdown-content">
                <ul style="width: 100%; padding-top: 8px;padding-left:0px;">
                  <li><span style="padding: 8px;"><a class="point" @click.prevent="view(covenant.covenant_id)" title="View">View</a></span></li>
                </ul>
              </div>
            </div>
            </td>
          </tr>           
        </tbody>
      </table>

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
        Nova.request().get('/nova-vendor/covenants/approved-covenants')
        .then(response => {
            this.covenants = response.data.compliance_covenants;
            this.viewOnly = response.data.viewOnly;
            this.isApprover = response.data.isApprover;
            setTimeout(() => {
              var table = $('#covenant-list').DataTable({
                  columns: [
                      { data: 'covenant_id',"width": "5%"},
                      { data: 'compliance_id',"width": "5%" },
                      { data: 'type',"width": "5%" },
                      { data: 'subType',"width": "15%" },
                      { data: 'description',"width": "15%" },
                      { data: 'frequency',"width": "6%" },
                      { data: 'startDate',"width": "12%" },
                      { data: 'dueDate',"width": "12%" },
                      { data: 'covenantStatus',"width": "15%" },
                      { data: 'name',"width": "15%" },
                      { title: "Actions", "defaultContent": "<button onclick='edititem();'>Edit</button>","width": "1%"},
                    ],
                    
                    "columnDefs": [
                        { "targets": [2,3,4,5,6,7], "searchable": true }
                    ],
                    order: [[4, 'desc']],
                    stateSave: false,

              });

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

</style>
