// var mydata = {
//     "1": 44,
//     "2": 40,
//     "3": 16,
// };
// var myCanvas = document.getElementById('circle-diagram')
// var partLength = (2 * Math.PI) /3;

// function drawPieSlice(ctx,centerX, centerY, radius, startAngle, endAngle, color ){
//   var xStart = centerX + Math.cos(startAngle) * radius;
//     var xEnd = centerX + Math.cos(startAngle + partLength) * radius;
//     var yStart = centerY + Math.sin(startAngle) * radius;
//     var yEnd = centerY + Math.sin(startAngle + partLength) * radius;
//    gradient = ctx.createLinearGradient(xStart, yStart, xEnd, yEnd);
//      gradient.addColorStop(0,   '#fff'); 
//     gradient.addColorStop(1,   color);
//       ctx.fillStyle=gradient;
//     ctx.beginPath();
//     ctx.moveTo(centerX,centerY);
//     ctx.arc(centerX, centerY, radius, startAngle, endAngle);
//     ctx.closePath();
//     ctx.fill();
//     ctx.lineWidth = 2;
//     ctx.strokeStyle = "#fff";
//     ctx.stroke();
// }
// var Piechart = function(options){
//     this.options = options;
//     this.canvas = options.canvas;
//     this.ctx = this.canvas.getContext("2d");
//     this.colors = options.colors;
//     this.draw = function(){
//         var total_value = 0;
//         var color_index = 0;
//         for (var categ in this.options.data){
//             var val = this.options.data[categ];
//             total_value += val;
//         }
 
//         var start_angle = 0;
//         for (categ in this.options.data){
//             val = this.options.data[categ];
//             var slice_angle = 2 * Math.PI * val / total_value;
 
//             drawPieSlice(
//                 this.ctx,
//                 this.canvas.width/2,
//                 this.canvas.height/2,
//                 Math.min(this.canvas.width/2,this.canvas.height/2),
//                 start_angle,
//                 start_angle+slice_angle,
//                 this.colors[color_index%this.colors.length]
//             );
 
//             start_angle += slice_angle;
//             color_index++;
//         }
 
 
//         if (this.options.doughnutHoleSize){
//             drawPieSlice(
//                 this.ctx,
//                 this.canvas.width/2,
//                 this.canvas.height/2,
//                 this.options.doughnutHoleSize * Math.min(this.canvas.width/2,this.canvas.height/2),
//                 0,
//                 2 * Math.PI,
//                 "#fff"
//             );
//         }
 
//     }
// }
// var myDougnutChart = new Piechart(
//     {
//         canvas:myCanvas,
//         data:mydata,
//         colors:["#717A7E","#FF9D67", "#218079"],
//         doughnutHoleSize:0.3
//     }
// );
// myDougnutChart.draw();