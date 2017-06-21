<template>
  <div class="container">
    <div class="blank20"></div>
    <h3>成功例案</h3>
    <div class="case-list">
      <el-row :gutter="10">

        <el-col :span="8">
          <el-card :body-style="{ padding: '0px' }" class="item">
            <div class="image-box">
              <a href="#" target="_blank">
                <img src="http://img3.imgtn.bdimg.com/it/u=1981414114,346497044&fm=214&gp=0.jpg">
              </a>
            </div>
            <div class="content">
              <a href="">好吃的汉堡</a>
              <div class="des">
                <p>几年来一直坚持做最专业的导航产品，是一家集研发、制造与销售于一体的世界级GPS品牌。产品涉及航空、航海,几年来一直坚持做最专业的导航产品，是一家集研发、制造与销售于一体的世界级GPS品牌。产品涉及航空、航海</p>
              </div>
            </div>
          </el-card>
        </el-col>


      </el-row>
    
    </div>
  </div>
</template>

<script>
export default {
  name: 'test',
  data () {
    return {
      msg: 'This is Stuff'
    }
  }
}

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .container{
    height: 500px;
  }
  .container h3 {
    font-size: 2rem;
    margin-bottom: 10px;
  }
  .case-list {
  
  }

  .item {
    height: 280px;
    margin: 5px 0;
  }

  .item img {
    width: 100%;
  }

  .image-box {
    height: 200px;
    overflow: hidden;
  }

  .content {
    padding: 10px;
  }
  .content a {
    font-size: 1.5rem;
  }

  .des {
    height: 30px;
    margin: 10px 0;
    overflow: hidden;
  }

  .des p {
    color: #666;
    font-size: 1.2rem;
    line-height: 1.3;
    text-overflow: ellipsis;
  }

</style>
