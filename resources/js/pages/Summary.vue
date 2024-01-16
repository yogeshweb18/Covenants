<template>
  <div id="app">
    <h1 class="text-90 font-normal text-xl md:text-2xl mb-3">List Of Covenants</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow" style="padding:20px;">
    <table class="w-full table-default listofcovenant-table" id="">
            <tr id="filter_col2" data-column="2">
                <td>Filter</td>
                <td align=""><input type="text" placeholder="Cl Code" class="column_filter filter-input" id="col2_filter" v-on:keyup="filterColumn(2)" ></td>
            </tr>
            <tr id="filter_col3" data-column="3">
                <td></td>
                <td align=""><input type="text" placeholder="Company Name" class="column_filter filter-input" id="col3_filter" v-on:keyup="filterColumn(3)"></td>
            </tr>
            <tr id="filter_col4" data-column="4">
                <td></td>
                <td align=""><input type="text" placeholder="Type" class="column_filter filter-input" id="col4_filter" v-on:keyup="filterColumn(4)"></td>
            </tr>
            <tr id="filter_col5" data-column="5">
                <td></td>
                <td align=""><input type="text" placeholder="Subtype" class="column_filter filter-input" id="col5_filter" v-on:keyup="filterColumn(5)"></td>
            </tr>
            
      </table>
      <table ref="dataTable" class="w-full table-default display tablescroll" id="covenant-list">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th></th>
            <th></th>
            <th>ClCode</th>
            <th>Company</th>
            <th>Type</th>
            <th>Sub type</th>
            <th>Frequency</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Target</th>
            <th>Actual</th>
            <th>Status</th>
            <th>Maker</th>
            <th>Actions</th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            
          </tr>
        </thead>
      </table>
      <div style="padding-top:30px;" v-if="isApprover == 1"><button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-2 h-9 text-sm flex-shrink-0" @click.prevent="multiMark('Approved')">Approve</button> <button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-2 h-9 text-sm flex-shrink-0" @click.prevent="multiMark('Rejected')">Reject</button>
      </div>
      <!-- <div style="padding-top:30px;" v-else-if="viewOnly == 1"><button class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-2 h-9 text-sm flex-shrink-0" @click.prevent="multiSubmitForApproval()">Submit For Approval</button>
      </div> -->
    </div>
    <div id="viewModal">
        <Modal
          v-show="isModalVisible"
          @close="closeModal"
          :selectedData="viewcompliance"
        />
        <Modalview
        v-show="isModalVisible"
        @close="closeModal"
        :selectedData="viewcompliance"
       />
    </div>
    <div>
      <Mark
          v-show="isMarkVisible"
          @close="closeMark"
          :approvalData="approvalData" 
          :status="approvalStatus"
        />
    </div>

  </div>
</template>

<script>

import { Link } from '@inertiajs/inertia-vue3';
import "datatables.net-dt/js/dataTables.dataTables";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import "datatables.net-rowgroup-dt/js/rowGroup.dataTables";
import "datatables.net-rowgroup-dt/css/rowGroup.dataTables.min.css";
import 'datatables.net-select';
import $ from 'jquery';
import Modalview from '../../../../../nova-components/Covenants/resources/js/pages/View.vue';
import Modal from '../../../../../nova-components/Covenants/resources/js/pages/Resolution.vue';
import Mark from '../../../../../nova-components/Covenants/resources/js/pages/MarkActive.vue';
import axios from 'axios';
export default {
  data() {
    return {
      covenants:[],
      viewCovenant: {},
      viewOnly: 1,
      isApprover: 0,
      viewcompliance: {},
      isModalVisible: false,
      isMarkVisible: false,
      selectedData: [],
      approvalData: [],
      approvalStatus: '',
    }
  },
  components: {
    Modalview,
    Modal,
    Mark
  },
  props: ['loadPage','viewOnly','isApprover'],
  methods: {
    populateDatatable() {
      setTimeout(() => {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          
        }
      });
      const self = this;
      if(this.loadPage == 1) {
        var urli = "/nova-vendor/covenants/active-list-pending";
      }
      else if(this.loadPage == 2) {
        var urli = "/nova-vendor/covenants/active-list-approved";
      }
      else {
        var urli = "/nova-vendor/covenants/active-list";
      }
      const url = urli;
      var table = $(this.$refs.dataTable).DataTable({
        "serverSide": true,
        "processing": true,
        "ajax":{
          "url": url,
          "dataType": "json",
          "type": "POST",
          "data": {_token:$('meta[name="csrf-token"]').attr('content')}
          },
          //oLanguage: {sProcessing: "<img src='/img/spinner.gif' />"},
          columns: [
              { data: 'instanceId','orderable':false},
              {
                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: '',
                },
              { data: 'clcode','orderable':true },
              { data: 'name', 'orderable':true},
              { data: 'type', 'orderable':true },
              { data: 'subType', 'orderable':true },
              { data: 'frequency', 'orderable':false },
              { data: 'activateDate', 'orderable':true },
              { data: 'dueDate', 'orderable':true },
              { data: 'targetValue', 'orderable':true },
              { data: 'resolution_value', 'orderable':true },
              { data: 'status', 'orderable':true },
              { data: 'username', 'orderable':true },
              
              { data: 'actions', 'orderable':false },
            ],

            order: [[6, 'asc']],
          
          rowCallback(row, data) {
            $(row).on('click', '.view-placeholder',() => {
              console.log('---------', JSON.stringify(data));
             console.log('///'+data.id);
                      self.newview(data.id);
              });
              $(row).on('click', '.resolve-placeholder',() => {
                self.view(data.id);
              });
              $(row).on('click', '.sfa-placeholder',() => {
                self.submitForApproval(data.id);
              });
              $(row).on('click', '.approve-placeholder',() => {
                self.mark('Approved',data.id);
              });
              $(row).on('click', '.reject-placeholder',() => {
                self.mark('Rejected',data.id);
              });
          },

            
               // order: [[1, 'asc']],
                
      });

      $('#covenant-list tbody').on('click', 'td.dt-control', function () {
              var tr = $(this).closest('tr');
              var row = table.row(tr);
       
              if (row.child.isShown()) {
                  // This row is already open - close it
                  row.child.hide();
                  tr.removeClass('shown');
              } else {
                  // Open this row
                  row.child(format(row.data())).show();
                  tr.addClass('shown');
              }
          });
      },200);

    },
     populateTable() {

      setTimeout(() => {

        
            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                
              }
            });
            
              var table = $('#covenant-list').DataTable({
                "serverSide": true,
                "processing": true,
                "ajax":{
                  "url": "/nova-vendor/covenants/active-list",
                  "dataType": "json",
                  "type": "POST",
                  "data": {_token:$('meta[name="csrf-token"]').attr('content')}
                  },
                  columns: [
                      { data: 'instanceId'},
                      { data: 'clcode' },
                      { data: 'name' },
                      { data: 'type' },
                      { data: 'subType' },
                      { data: 'frequency' },
                      { data: 'activateDate' },
                      { data: 'dueDate' },
                      { data: 'targetValue' },
                      { data: 'resolution_value' },
                      { data: 'status' },
                      { data: 'username' },
                      { data: 'actions' },
                    ],
                  columnDefs: [ {
                  orderable: false,
                  className: 'select-checkbox',
                  targets:   0,
                  "deferRender": true,
                  
              } ],
              select: {
                  style:    'os',
                  selector: 'td:first-child',
                  page: 'current'
              },
                    
                   // order: [[1, 'asc']],
                    stateSave: false,
                    
              });

              

            },200);
     },

      format(d) {
        return (
            '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<thead><tr><th>Description</th><th>Inconsistency Treatment</th><th>Comments</th><th>ISIN</th></tr></thead>'+
            '<tbody>' +
            '<tr>' +
            '<td>' +
            d.description +
            '</td>' +
            '<td>' +
            d.inconsistencyTreatment +
            '</td>' +
            '<td>' +
            d.comments +
            '</td>' +
            '<td>' +
            d.isins +
            '</td>' +
            '</tr>' +
            '</tbody>' +
            '</table>'
        );
    },

       filterColumn(i) {
        $('#covenant-list')
            .DataTable()
            .column(i)
            .search(
                $('#col' + i + '_filter').val(),
                //$('#col' + i + '_smart').prop('checked')
            )
            .draw();
    },
    newview(id) {
      console.log('Logging ID----', id);
        Nova.request().post('/nova-vendor/covenants/view',{'id':id})
        .then(response => {
            if(response.data.status == 'success') {
              this.viewCovenant = response.data.covenant;
              this.isModalVisible = true;
            }            
        });
      },
    view(id) {
          Nova.request().post('/nova-vendor/covenants/resolution',{'id':id})
          .then(response => {
              if(response.data.status == 'success') {
                this.viewcompliance = response.data.instance;
                this.isModalVisible = true;
              }            
          });
        },

      submitForApproval(id) {
        var ids = [id];
        Nova.request().post('/nova-vendor/covenants/submitForApprovalActive',{'id':ids})
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
        if(ids.length == 0) {
          alert('No record is selected for this action.');
          return false;
        }
        Nova.request().post('/nova-vendor/covenants/submitForApprovalActive',{'id':ids})
        .then(response => {
            if(response.data.success) {
              location.reload();
            }
        });
      },

      mark(status,id) {
          this.approvalData = [id];
          this.approvalStatus = status;
          this.isMarkVisible = true;  
      },

      multiMark(status) {
          var temp = [];
          $("input[type='checkbox'][name='checkApproval[]']:checked").each(function () {
              temp.push($(this).val());
          });
          this.approvalData = temp;
          this.approvalStatus = status;
          this.isMarkVisible = true;  
      },

        closeModal() {
          this.isModalVisible = false;
        },

        closeMark() {
          this.isMarkVisible = false;
        },

        selectAll(e) {
          var table = $('#covenant-list').DataTable();
          if ($('#checkAll').is( ":checked" )) {
            table.rows(  ).select();        
        } else {
            table.rows(  ).deselect(); 
        }
        },
  },
  created:function(){
    this.populateDatatable();
    
  },
  /*async mounted() {
    //this.getCompliance();
    try {
      const response = await axios.post('/nova-vendor/covenants/active-list');
      this.$refs.dataTable.DataTable().clear();
      this.$refs.dataTable.DataTable().rows.add(response.data);
      this.$refs.dataTable.DataTable().draw();
    } catch (error) {
      console.log(error);
    }
  },*/
  mounted() {
    //this.getCompliance();
   
  },
}
// function format(d) {
//         return (
//             '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;width:100%">' +
//             '<thead><tr><th>Description</th><th>Inconsistency Treatment</th><th>Comments</th><th>ISIN</th></tr></thead>'+
//             '<tbody>' +
//             '<tr>' +
//             '<td>' +
//             d.description +
//             '</td>' +
//             '<td>' +
//             d.inconsistencyTreatment +
//             '</td>' +
//             '<td>' +
//             d.comments +
//             '</td>' +
//             '<td>' +
//             d.isins +
//             '</td>' +
//             '</tr>' +
//             '</tbody>' +
//             '</table>'
//         );
//     }
</script>

<style>
thead {
    position: sticky;
    top: 0px;
  }
</style>
