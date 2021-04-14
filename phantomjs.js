// /usr/local/bin/phantomjs /var/www/taskusers/data/www/taskusers.com/catalog/controller/pril/phantomjs.js 200 https://user.intronex.ru andrey 111333555777
var system = require('system');
var args = system.args;

if (args.length === 1) {
	console.log('Try to pass some arguments when invoking this script!');
} else {
	args.forEach(function(arg, i) {
    //console.log(i + ': ' + arg);
	});
}
 
var size_x = 1024;
var size_y = 800;
var page = require('webpage').create();
var page1 = require('webpage').create();
var page2 = require('webpage').create();

page.viewportSize = {width: size_x, height: size_y};//задаем размер
set = {
	javascriptEnabled: true,
	encoding: "UTF-8",
	userAgent: 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
};
phantom.cookiesEnabled = true;
//console.log("2: " + args[2] + "/oper/uzel.php?type=vols&code=" + Number(args[1]));
page.open(args[2] + "/oper/", set, function(status) {
  //console.log("3: Status: " + status);	
  if (status === "success") {
    page.evaluate(function(login, passw) {
        document.getElementsByTagName('input')[1].value = login;
		document.getElementsByTagName('input')[2].value = passw;
		document.getElementsByTagName('input')[3].click();
    }, args[3], args[4]);
	//console.log("4: Autorization");
	
	
	window.setTimeout(function() {
		page1.open(args[2] + "/oper/index.php?core_section=node&action=show_commutation&id=" + Number(args[1]), set, function(status) {
			//console.log("5: Status: " + status);	
			if (status === "success") {
				var ua = page1.evaluate(function () {
					//return document.getElementsByTagName('html')[0].outerHTML;
					var divElem = document.getElementById('map_menu_panel');
					return divElem.getElementsByTagName('a')[2].textContent;
				});
				//console.log("5: " + ua);
				page1.evaluate(function() {
					var divElement = document.getElementById('map_menu_panel');
					divElement.getElementsByTagName('a')[2].click();
				});
				
				size_x = page1.evaluate(function () {
					return document.querySelector("input[name=size_x]").value;
				});
				size_y = page1.evaluate(function () {
					return document.querySelector("input[name=size_y]").value;
				});

				/*var svg = page1.evaluate(function() {
					var svgElement = document.getElementById('fmaps');
									
					var SVG = svgElement.querySelector('svg');
					var size_x = SVG.querySelector('rect').getAttribute("width");
					var size_y = SVG.querySelector('rect').getAttribute("height");

					return new Array(size_x, size_y);
				});*/

				//console.log("6: " + (Number(svg[0]) + 50) + " " + (Number(svg[1]) + 50));
				//page2.viewportSize = {width: Number(svg[0]) + 50, height: Number(svg[1]) + 50};//задаем размер
				page2.viewportSize = {width: Number(size_x) + 50, height: Number(size_y) + 50};
	
				page2.open(args[2] + "/oper/index.php?core_section=node&action=show_commutation&id=" + args[1], set, function(status) {
					//console.log("7: Status: " + status);		
					if (status === "success") {
						page2.render("image/catalog/img-comm/" + args[1] + ".png");
						//console.log("8: End 2.");
						phantom.exit();
					}
				});

			}
		});
		
	}, 4500);
	
  }
});