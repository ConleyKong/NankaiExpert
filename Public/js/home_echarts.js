function makeChart(id, option){
  var myChart = echarts.init(document.getElementById(id), 'macarons');
  myChart.setOption(option);
  window.onresize = myChart.resize;
}

function createRandom(arrayLength){
  var array = [];
  for (var i = 0; i < arrayLength;i++)
    array.push(Math.floor(20+Math.random()*(180-20))); 
  return array;
}
var option5 = {
    timeline:{
        data:[
            '2010-01-01','2011-01-01','2012-01-01','2013-01-01','2014-01-01','2015-01-01'
        ],
        label : {
            formatter : function(s) {
                return s.slice(0, 4);
            }
        },
        autoPlay : true,
        playInterval : 1500
    },
    options:[
        {
            title : {
                'text':'2010年项目统计',
                'subtext':'数据来自南开大学专家信息管理系统'
            },
            tooltip : {'trigger':'axis'},
            legend : {
                x:'right',
                'data':['启动','完成']
            },
            toolbox : {
                'show':true, 
                orient : 'vertical',
                x: 'right', 
                y: 'center',
                'feature':{
                    'magicType':{'show':true,'type':['line','bar','stack','tiled']},
                    'restore':{'show':true},
                    'saveAsImage':{'show':true}
                }
            },
            calculable : true,
            grid : {'y':80,'y2':100},
            xAxis : [{
                'type':'category',
                'axisLabel':{'interval':0},
                'data':[
                    '计控学院','医学院','化学学院','物理学院','生命科学学院','组合中心','数学所','统计研究院','软件学院','材料科学学院'
                ]
            }],
            yAxis : [
                {
                    'type':'value',
                    'name':'启动',
                    'max':200
                },
                {
                    'type':'value',
                    'name':'完成',
                    'max':200
                }
            ],
            series : [
                {
                    'name':'启动',
                    'type':'bar',
                    'data': createRandom(10),
                },
                {
                    'name':'完成','yAxisIndex':1,'type':'bar',
                    'data': createRandom(10),
                },
            ]
        },
        {
            title : {'text':'2011年项目统计'},
            series : [
                {'data': createRandom(10)},
                {'data': createRandom(10)},
            ]
        },
        {
            title : {'text':'2012年项目统计'},
            series : [
                {'data': createRandom(10)},
                {'data': createRandom(10)},
            ]
        },
        {
            title : {'text':'2013年项目统计'},
            series : [
                {'data': createRandom(10)},
                {'data': createRandom(10)},
            ]
        },
        {
            title : {'text':'2014年项目统计'},
            series : [
                {'data': createRandom(10)},
                {'data': createRandom(10)},
            ]
        },
        {
            title : {'text':'2015年项目统计'},
            series : [
                {'data': createRandom(10)},
                {'data': createRandom(10)},
            ]
        }
    ]
};

                    
var option1 = {
    color : [
        'rgba(255, 69, 0, 0.5)',
        'rgba(255, 150, 0, 0.5)',
        'rgba(255, 200, 0, 0.5)',
        'rgba(155, 200, 50, 0.5)',
        'rgba(55, 200, 100, 0.5)'
    ],
    title : {
        text: '研究项目概况',
        subtext: '2010年至今'
    },
    tooltip : {
        trigger: 'item',
        formatter: "{a} <br/>{b} : {c}%"
    },
    toolbox: {
        show : true,
        feature : {
            //mark : {show: true},
            dataView : {show: true, readOnly: false},
            restore : {show: true},
            saveAsImage : {show: true}
        }
    },
    legend: {
        data : ['参与','完成','经费','分配','数量']
    },
    series : [
        {
            name:'业务指标',
            type:'gauge',
            center: ['25%','60%'],
            splitNumber: 10,       // 分割段数，默认为5
            axisLine: {            // 坐标轴线
                lineStyle: {       // 属性lineStyle控制线条样式
                    color: [[0.2, '#228b22'],[0.8, '#48b'],[1, '#ff4500']], 
                    width: 8
                }
            },
            axisTick: {            // 坐标轴小标记
                splitNumber: 10,   // 每份split细分多少段
                length :12,        // 属性length控制线长
                lineStyle: {       // 属性lineStyle控制线条样式
                    color: 'auto'
                }
            },
            axisLabel: {           // 坐标轴文本标签，详见axis.axisLabel
                textStyle: {       // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                    color: 'auto'
                }
            },
            splitLine: {           // 分隔线
                show: true,        // 默认显示，属性show控制显示与否
                length :30,         // 属性length控制线长
                lineStyle: {       // 属性lineStyle（详见lineStyle）控制线条样式
                    color: 'auto'
                }
            },
            pointer : {
                width : 5
            },
            title : {
                show : true,
                offsetCenter: [0, '-40%'],       // x, y，单位px
                textStyle: {       // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                    fontWeight: 'bolder'
                }
            },
            detail : {
                formatter:'{value}%',
                textStyle: {       // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                    color: 'auto',
                    fontWeight: 'bolder'
                }
            },
            data:[{value: 68, name: '完成率'}]
        },
        {
            name:'预期',
            type:'funnel',
            x: '45%',
            width: '45%',
            itemStyle: {
                normal: {
                    label: {
                        formatter: '{b}预期'
                    },
                    labelLine: {
                        show : false
                    }
                },
                emphasis: {
                    label: {
                        position:'inside',
                        formatter: '{b}预期 : {c}%'
                    }
                }
            },
            data:[
                {value:60, name:'经费'},
                {value:40, name:'分配'},
                {value:20, name:'数量'},
                {value:80, name:'完成'},
                {value:100, name:'参与'}
            ]
        },
        {
            name:'实际',
            type:'funnel',
            x: '45%',
            width: '45%',
            maxSize: '80%',
            itemStyle: {
                normal: {
                    borderColor: '#fff',
                    borderWidth: 2,
                    label: {
                        position: 'inside',
                        formatter: '{c}%',
                        textStyle: {
                            color: '#fff'
                        }
                    }
                },
                emphasis: {
                    label: {
                        position:'inside',
                        formatter: '{b}实际 : {c}%'
                    }
                }
            },
            data:[
                {value:30, name:'经费'},
                {value:10, name:'分配'},
                {value:5, name:'数量'},
                {value:50, name:'完成'},
                {value:80, name:'参与'}
            ]
        }
    ]
};

makeChart('c1', option1); 
//makeChart('c2', option5);

var option2 = {
  title : {
      text: 'Search times of visitors recently',
      subtext: 'From last week'
  },
  tooltip : {
      trigger: 'axis'
  },
  xAxis : [
      {
        type : 'category',
        boundaryGap : false,
        data : ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']
      }
  ],
  yAxis : [
      {
        type : 'value'
      }
  ],
  series : [
    {
      name:'search times',
      type:'line',
      smooth:true,
      itemStyle: {normal: {areaStyle: {type: 'default'}}},
      data:[200, 120, 400, 230, 360, 730, 710]
    }
  ]
};  
makeChart('c3', option5); 

makeChart('c4', option2);