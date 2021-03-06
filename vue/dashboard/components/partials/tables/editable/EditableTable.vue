<template>
  <div>
    <div class="table-container">

      <a @click="expanded = ! expanded" class="button is-outlined" id="expand">
            <span class="icon is-small">
              <i :class="'fas ' + (expanded ? 'fa-compress-alt' : 'fa-expand-alt')"></i>
            </span>
        <span>{{ expanded ? "Collapse" : "Expand" }}</span>
      </a>

      <!-- 'Saved' message -->
      <transition name="fade">
        <span class="saved has-text-success" v-if="showSaved">
          <span class="icon">
            <i class="fas fa-check-circle"></i>
          </span>
          Saved
        </span>
      </transition>

      <!-- 'Deleting...' message with undo button -->
      <transition name="fade">
        <span class="deleting has-text-danger" v-if="deleting">
          <span class="icon">
            <i class="fas fa-spinner"></i>
          </span>
          Deleting... <a @click="$emit('undoDelete')">Undo</a>
        </span>
      </transition>

      <table class="table is-hoverable">
        <thead>
        <tr>
          <th v-for="heading in tableHeadings">{{ heading }}</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="row in rows">
          <EditableField
              v-for="(cell, index) in row"
              :key="index"
              v-if="index < tableHeadings.length"
              :value="cell.value"
              :type="cell.type"
              :editable="! (cell.editable !== undefined && cell.editable === false)"
              :editing="cell.editing"
              @cellUpdated="$emit('cellUpdated', {id: row.consumerId, key: cell.field, value: $event.value})"
          >
          </EditableField>
          <td class="delete-btn">
            <transition name="fade">
              <a v-if="! deleting" @click="$emit('deleteRow', row)" class="has-text-danger">
              <span class="icon">
                <i class="fas fa-trash"></i>
              </span>
              </a>
            </transition>
          </td>
        </tr>
        <tr>
          <td :colspan="tableHeadings.length + 1">
            <a @click="$emit('addRow')">
              <span class="icon">
                <i class="fas fa-plus"></i>
              </span>
              <span>{{addText}}</span>
            </a>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination bar -->
    <Pagination
        v-if="pagination"
        :itemsTotal="itemsTotal"
        :itemsPerPage="itemsPerPage"
        :currentPage="currentPage"
        :buttonsMax="5"
        :url="paginationUrl"
    ></Pagination>
  </div>
</template>

<script>
import Pagination from "vue-bulma-paginate";
import EditableField from "./fields/EditableField";

export default {
  name: "EditableTable",
  components: {
    EditableField,
    Pagination
  },
  computed: {
    tableHeadings() {
      if (this.expanded) {
        return this.headings.concat(this.expandedHeadings);
      }
      return this.headings;
    }
  },
  data() {
    return {
      expanded: false,
    }
  },
  props: {
    headings: {
      type: Array,
      required: true
    },
    expandedHeadings: Array,
    rows: Array,
    pagination: {
      type: Boolean,
      required: false,
      default: false
    },
    itemsPerPage: Number,
    currentPage: Number,
    itemsTotal: Number,
    paginationUrl: String,
    showSaved: {
      type: Boolean,
      required: false,
      default: false
    },
    addText: {
      type: String,
      required: false,
      default: 'Add'
    },
    deleting: {
      type: Boolean,
      required: false,
      default: false
    }
  },
}
</script>

<style scoped lang="scss">
#lti-dashboard-app {
  #expand {
    float: left;
    height: 1.5rem;
    margin-bottom: .5rem;
  }

  .saved, .deleting {
    float: right;
    font-style: italic;
    margin-right: 1rem;
  }

  td {
    min-width: 7rem;
    white-space: nowrap;
  }

  .fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
  }

  .deleting .icon > i {
    animation-name: spin;
    animation-duration: 2000ms;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
  }
}
</style>
