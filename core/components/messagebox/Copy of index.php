<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>jQuery MsgBox &raquo; Examples</title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

  <link rel="stylesheet" type="text/css" href="css/reset.css" />
  <link rel="stylesheet" type="text/css" href="css/global.css" />



  <script type="text/javascript" src="javascript/jquery.min.js"></script>
  <link rel="stylesheet" type="text/css" href="javascript/msgbox/jquery.msgbox.css" />
  <script type="text/javascript" src="javascript/msgbox/jquery.msgbox.min.js"></script>

</head>

<body>
  <div id="top">
    <div id="header">
        <h1>jQuery MsgBox <span>v1.0</span></h1>
    </div>

    <div class="hastoc">

        <p>MsgBox is compatible and fully tested with Safari 4+, Internet Explorer 6+, Firefox 2+, Google Chrome 3+, Opera 9+. </p>

        <p class="center"><img src="assets/jquerymsgbox.jpg" alt=""/></p>
        
        <div id="wrapper">
          <h2>Examples</h2>

          <h4>Example 1</h4>
          <div class="indent">

          <p class="center"><img src="assets/ex1.jpg" alt=""/></p>
          <p class="right"><button onclick='$.msgbox("The selection includes process white objects. Overprinting such objects is only useful in combination with transparency effects.");'>Example 1</button></p>
          <p>To simply call MsgBox like you would a regular alert command:</p>
<pre>
$.msgbox("The selection includes process white objects. Overprinting such objects is only useful in combination with transparency effects.");
</pre>
          
          </div>
          <h4>Example 2</h4>
          <div class="indent">
          <p class="center"><img src="assets/ex2.jpg" alt=""/></p>
          <p class="right"><button onclick='$.msgbox("Are you sure that you want to permanently delete the selected element?", {type: "confirm",buttons : [{type: "submit", value: "Yes"},{type: "submit", value: "No"},{type: "cancel", value: "Cancel"}]}, function(result) { $("#result2").text(result); });'>Example 2</button></p>
          <p>To add a couple extra buttons with different values:</p>
<pre>
$.msgbox("Are you sure that you want to permanently delete the selected element?", {
  type: "confirm",
  buttons : [
    {type: "submit", value: "Yes"},
    {type: "submit", value: "No"},
    {type: "cancel", value: "Cancel"}
  ]
}, function(result) {
  $("#result2").text(result);
});
</pre>
<pre>
Result: <span id="result2"></span>
</pre>









          </div>
          <h4>Example 3</h4>
          <div class="indent">
          <p class="center"><img src="assets/ex3.jpg"  alt=""/></p>
          <p class="right"><button onclick='$.msgbox("jQuery is a fast and concise JavaScript Library that simplifies HTML document traversing, event handling, animating, and Ajax interactions for rapid web development.", {type: "info"});'>Example 3</button></p>
<pre>
$.msgbox("jQuery is a fast and concise JavaScript Library that simplifies HTML document traversing, event handling, animating, and Ajax interactions for rapid web development.", {type: "info"});
</pre>










          </div>
          <h4>Example 4</h4>
          <div class="indent">
          <p class="center"><img src="assets/ex4.jpg"  alt=""/></p>
          <p class="right"><button onclick='$.msgbox("An error 1053 ocurred while perfoming this service operation on the MySql Server service.", {type: "error"});'>Example 4</button></p>
<pre>
$.msgbox("An error 1053 ocurred while perfoming this service operation on the MySql Server service.", {type: "error"});
</pre>




          </div>
          <h4>Example 5</h4>
          <div class="indent">
          <p class="center"><img src="assets/ex5.jpg"  alt=""/></p>
          <p class="right"><button onclick='$.msgbox("Insert your name below:", {type: "prompt"}, function(result) {if (result) {alert("Hello "+result);}});'>Example 5</button></p>
<pre>
$.msgbox("Insert your name below:", {
  type: "prompt"
}, function(result) {
  if (result) {
    alert("Hello " + result);
  }
});
</pre>

          </div>


          <h2>Advanced Examples</h2>

          <h4>Advanced Example 1</h4>
          <div class="indent">
          <p class="center"><img src="assets/aex1.jpg"  alt=""/></p>
          <p class="right"><button id="advancedexample1">Example 1</button></p>
<pre>
$("#advancedexample1").click(function() {
  $.msgbox("&lt;p&gt;In order to process your request you must provide the following:&lt;/p&gt;", {
    type    : "prompt",
    inputs  : [
      {type: "text",     label: "Insert your Name:", value: "George", required: true},
      {type: "password", label: "Insert your Password:", required: true}
    ],
    buttons : [
      {type: "submit", value: "OK"},
      {type: "cancel", value: "Exit"}
    ]
  }, function(name, password) {
    if (name) {
      $.msgbox("Hello &lt;strong&gt;"+name+"&lt;/strong&gt;, your password is &lt;strong&gt;"+password+"&lt;/strong&gt;.", {type: "info"});
    } else {
      $.msgbox("Bye!", {type: "info"});
    }
  });
});
</pre>
          </div>



<script type="text/javascript">
$("#advancedexample1").click(function() {
  $.msgbox("<p>In order to process your request you must provide the following:</p>", {
    type    : "prompt",
    inputs  : [
      {type: "text",     label: "Insert your Name:", value: "George", required: true},
      {type: "password", label: "Insert your Password:", required: true}
    ],
    buttons : [
      {type: "submit", value: "OK"},
      {type: "cancel", value: "Exit"}
    ]
  }, function(name, password) {
    if (name) {
      $.msgbox("Hello <strong>"+name+"</strong>, your password is <strong>"+password+"</strong>.", {type: "info"});
    } else {
      $.msgbox("Bye!", {type: "info"});
    }
  });
});
</script>

        </div>

    </div><!--/hastoc-->
  </div>
  <!-- Syntax highlighting Javascript http://www.howtocreate.co.uk/tutorials/jsexamples/syntax/ -->
</body>
</html>