<template>
  <div class="app-container">
    <el-form ref="form" :model="form" label-width="80px">
      <el-form-item label="新闻标题">
        <el-input v-model="form.title"></el-input>
      </el-form-item>
      <el-form-item label="新闻时间">
        <el-input v-model="form.news_time"></el-input>
      </el-form-item>
      <el-form-item label="新闻内容">
        <el-input type="textarea" :rows="30"  v-model="form.content"></el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary"  @click="onSubmit">保存</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { getDetail, modifyNews } from '@/api/news'

export default {
  data() {
    return {
      listLoading: true,
      questId: '',
      form: {
        title: '',
        news_time: '',
        content: '',
        id: ''
      }
    }
  },
  filters: {
  },
  created() {
    this.questId = Number(this.$route.query.id)
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.listLoading = true
      getDetail({ id: this.questId }).then(response => {
        this.form = response.data.list
        this.listLoading = false
      }).catch(error => {
        this.$message({ message: error, type: 'error' })
        this.listLoading = false
      })
    },
    onSubmit() {
      modifyNews(this.form).then(response => {
        if (response.status === 200) {
          this.$message({ message: '修改成功', type: 'success' })
        }
        this.listLoading = false
      }).catch(error => {
        this.$message({ message: error, type: 'error' })
        this.listLoading = false
      })
    }
  }
}
</script>
