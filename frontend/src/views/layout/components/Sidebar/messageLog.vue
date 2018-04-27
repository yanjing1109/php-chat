<template>
  <div class="menu-wrapper">
    <el-row v-for="item in list">
      <el-col :span="10" class="user">{{ item.token }}：</el-col>
      <el-col :span="14" style="color: #E6A23C">{{ item.message }}</el-col>
    </el-row>
  </div>
</template>

<script>
import EventBus from '@/events/event-bus.js'

export default {
  data() {
    return {
      list: [
        {
          token: '小明',
          message: '什么时间开始直播那'
        },
        {
          token: '小明',
          message: '快开播啦'
        }
      ]
    }
  },
  created() {
    this.registerEvent()
  },

  destroyed() {
    this.destroyEvent()
  },
  methods: {
    registerEvent() {
      EventBus.$on('add-message', (data) => {
        this.list.push(data)
      })
    },

    destroyEvent() {
      EventBus.$off('add-message')
    }
  }
}
</script>
<style>
  .user {
    display: inline-block;
    max-width: 8em;
    /*overflow: hidden;*/
    /*white-space: nowrap;*/
    /*word-wrap: normal;*/
    text-overflow: ellipsis;
    color: #3c9cfe;
    cursor: pointer;
  }
</style>
