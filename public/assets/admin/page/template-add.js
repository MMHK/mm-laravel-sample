import Vue from "vue";
import axios from "axios";
import VeeValidate from 'vee-validate';
Vue.use(VeeValidate);
var vm = new Vue({
  el: "#add-broadcast",
  data: function() {
    return {
      is_header: false, //是否展示header
      is_media: false, //是否是media_header
      is_text: false, //是否是text类型
      is_body: false, //是否展示body
      is_footer: false, //是否展示footer
      is_bottons: false, //是否展示bottons
      is_quick_reply: false,
      is_dynamic: false,
      is_dynamic_params: false,
      is_static: false,
      template_list: PAGE.meta.template,
      select_template: "",
      t_preview: "",
      header: null,
      body: "",
      footer: "",
      buttons: "",
      hsm: "",
      body_count: 0,
      footer_count: 0,
      buttons_count: 0,
      button_index: 0,
      button_text: "",
      button_url: "",
      main_url: "",
      static_url_text: "",
      dynamic_url_text: "",
      select_file:"",//选择上传的文件
      t_body:"",
      t_footer:"",
      t_quick_reply:"",
      dynamic_param:"",
      value: '',
      select_data_source: "",
      data_source_list:""
    };
  },
  mounted: function() {},
  methods: {
    //判断{{}}字符串出现次数
    matchCount: function(s, re) {
      re = eval("/" + re + "/ig");
      return s.match(re) ? s.match(re).length : 0;
    },

    //选择模板
    getTemplate: function(select_template) {
      const _this = this;
      _this.is_header= false,
      _this.is_body = false, 
      _this.is_footer = false, 
      _this.is_bottons = false, 
      axios
        .post(API_URI + "/broadcast/temp", { temp_name: select_template })
        .then(function(res) {
          console.log(res);
          console.log(res.data);
          _this.header = res.data.data.header;
          _this.body = res.data.data.body;
          _this.footer = res.data.data.footer;
          _this.buttons = res.data.data.buttons;
          _this.hsm = res.data.data.hsm;

          if (_this.hsm != null) {
            _this.t_preview = _this.hsm;
          } else {
            _this.t_preview = _this.body;
          }

          if (_this.header != null){
              _this.is_header = true;
          }
          if(_this.header != null &&_this.header.typeString != "text"){
            _this.is_media = true;
          } else{
            _this.is_text = true;
          }

          if (_this.body != null) {
            _this.body_count = _this.matchCount(_this.body, "{{");
            if (_this.body_count > 0) {
              _this.is_body = true;
            }
          }

          if (_this.is_media && _this.footer != null) {
            _this.footer_count = _this.matchCount(_this.footer, "{{");
            if (_this.footer_count > 0) {
              _this.is_footer = true;
            }
          }

          if (_this.buttons != null) {
            _this.is_bottons = true;
            for (var btn_index in _this.buttons) {
              _this.button_index = _this.buttons[btn_index];
              _this.button_text = _this.button_index.parameter.text;
              _this.button_url = _this.button_index.parameter.url;
            }
            if (_this.button_index.type == "quick_reply") {
              _this.is_quick_reply = true;
            } else {
              if (_this.button_index.parameter.urlType == "dynamic") {
                //截取url固定的部分
                _this.main_url = _this.button_url.split("{{1}}")[0];
                _this.dynamic_url_text = _this.main_url;
                _this.buttons_count = _this.matchCount(_this.button_url, "{{");
                if (_this.buttons_count > 0) {
                  _this.is_dynamic = true;
                } 
              }else {
                _this.is_static = true;
                _this.static_url_text = _this.button_url;
              }
            }
          }
        })
        // .catch(function(error) {
        //   console.log(error);
        // });
    },
    //上传文件
    upload_file: function (){
      const _this = this;
      _this.$refs.html5file.click()
      _this.upload_change();
    },
    
    upload_change: function (){
      const _this = this;
      let input_file = _this.$refs.html5file;
      _this.select_file = input_file.files[0];
      if(input_file.files.length<=0){
          return;
      }
      let formData = new FormData();
      formData.append('file', _this.select_file); 
      formData.append('sync',1); 
        axios({
            url: API_URI +"/upload/save",
            data: formData,
            dataType: "json",
            method: "POST",
            contentType: false,
            processData: false,
        }).then(function(res) {
            // console.log(res);
            if(res.data.status==1){
                alert('上传成功')
                _this.$refs.input_url.placeholder= res.data.data.url;
            }else{
                alert('上传失败')
            }
        })
    },

    // //非空验证
    // validate_form:function(){

    // },
    

    //提交
    submit_template: function(){
      console.log("点击提交按钮");
      var _this = this;
      _this.$validator.validateAll('result').then(function (res) {
      console.log(res)
    });


      // if(!this.select_template.trim()){
      //   alert("template不能为空")
      //   return;
      // }
      // if(!this.t_body.trim()){
      //   alert("template-body 不能为空")
      //   return;
      // }
      // if(!this.t_footer.trim()){
      //   alert("t_footer不能为空")
      //   return;
      // }
      // if(!this.t_quick_reply.trim()){
      //   alert("t_quick_reply不能为空")
      //   return;
      // }
      // if(!this.dynamic_param.trim()){
      //   alert("dynamic_param不能为空")
      //   return;
      // }
    }
  },
});
