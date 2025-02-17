<template>
    <div v-if="loading">
      Loading leads...
    </div>
    
    <div v-else>
      <div class="kanban-board">
        <div v-for="(status, index) in boardStatuses" :key="index" class="kanban-column">
          <h3>{{ status.name }}</h3>
          <div v-for="lead in filteredLeads(status.id)" :key="lead.id" class="kanban-card" draggable="true" @dragstart="onDragStart($event, lead)" @dragover="onDragOver($event)" @drop="onDrop($event, status.id)">
            <div>{{ lead.name }}</div>
            <div>{{ lead.email }}</div>
          </div>
        </div>
      </div>
  
      <!-- Display statistics -->
      <div>
        <h4>Top Conversion Counselors</h4>
        <ul>
          <li v-for="counselor in topConversionCounselors" :key="counselor.id">
            {{ counselor.name }} - Conversions: {{ counselor.total_conversions }}
          </li>
        </ul>
  
        <h4>Leads per Counselor</h4>
        <ul>
          <li v-for="counselor in leadsPerCounselor" :key="counselor.id">
            {{ counselor.name }} - Leads: {{ counselor.total_leads }}
          </li>
        </ul>
  
        <h4>Most Active Counselor</h4>
        <div>
          <p>{{ mostActiveCounselor.name }}</p>
          <p>Total Conversions: {{ mostActiveCounselor.total_conversions }}</p>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        boardStatuses: [
          { id: 1, name: 'New Leads' },
          { id: 2, name: 'In Progress' },
          { id: 3, name: 'Converted' },
        ],
        leads: [],
        topConversionCounselors: [],
        leadsPerCounselor: [],
        mostActiveCounselor: {},
        draggedLead: null,
        loading: true,
        base_url: 'http://localhost:8000',
      };
    },
    async mounted() {
      if (!localStorage.getItem('access_token')) {
        console.error('Access token not found. Redirecting to login...');
        return;
      }
      await this.fetchLeads();
      await this.fetchAdditionalData(); // Fetch additional data
    },
    methods: {
      async fetchLeads() {
        try {
          const response = await axios.get(`${this.base_url}/api/reports/leads-per-counselor`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
          });
          if (Array.isArray(response.data)) {
            this.leads = response.data;
          } else {
            console.error('Expected an array for leads data, but got:', response.data);
          }
        } catch (error) {
          console.error('Error fetching leads:', error);
          if (error.response && error.response.status === 401) {
            console.error('Unauthorized. Redirecting to login...');
          }
        }
      },
  
      async fetchAdditionalData() {
        try {
          const apiCalls = [
            axios.get(`${this.base_url}/api/reports/top-conversion-counselors`, {
              headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
            }),
            axios.get(`${this.base_url}/api/reports/leads-per-counselor`, {
              headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
            }),
            axios.get(`${this.base_url}/api/reports/most-active-counselor`, {
              headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
            }),
          ];
  
          const [response1, response2, response3] = await Promise.all(apiCalls);
          this.topConversionCounselors = response1.data;
          this.leadsPerCounselor = response2.data;
          this.mostActiveCounselor = response3.data;
  
        } catch (error) {
          console.error('Error fetching additional data:', error);
        } finally {
          this.loading = false;  // Set loading to false once the data is fetched
        }
      },
  
      filteredLeads(statusId) {
        return this.leads.filter((lead) => lead.status === statusId);
      },
  
      onDragStart(event, lead) {
        this.draggedLead = lead;
        event.dataTransfer.setData('text/plain', lead.id);
        event.target.classList.add('dragging');
      },
  
      onDragOver(event) {
        event.preventDefault();
        event.target.classList.add('drag-over');
      },
  
      onDrop(event, statusId) {
        event.preventDefault();
        if (this.draggedLead) {
          this.updateLeadStatus(this.draggedLead.id, statusId);
        }
        event.target.classList.remove('drag-over');
      },
  
      async updateLeadStatus(leadId, statusId) {
        try {
          await axios.put(
            `${this.base_url}/api/leads/${leadId}`,
            { status: statusId },
            { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } }
          );
          const leadIndex = this.leads.findIndex((lead) => lead.id === leadId);
          if (leadIndex !== -1) {
            this.leads[leadIndex].status = statusId;
          }
        } catch (error) {
          console.error('Error updating lead status:', error);
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .kanban-board {
    display: flex;
    justify-content: space-between;
    margin: 20px;
  }
  
  .kanban-column {
    width: 30%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #f9f9f9;
  }
  
  .kanban-card {
    background-color: #fff;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: move;
    transition: background-color 0.3s ease;
  }
  
  .kanban-card:hover {
    background-color: #f0f0f0;
  }
  
  .dragging {
    opacity: 0.5;
  }
  
  .drag-over {
    background-color: #e0f7fa;
  }
  </style>
  