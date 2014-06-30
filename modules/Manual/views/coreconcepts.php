<h1>Core Concepts</h1>

<h3 id="static">All static</h3>
<p>All controller functions are static, you do not need any instantiation at any time. So you can access your controllers across each other easily.</p>
<pre class="prettyprint">
// in view App/index.php
&lt;html&gt; ...
    &lt;div&gt;
    &lt;?php Manual::sideMenu() ?&gt;
    &lt;/div&gt;
&lt;/html&gt;
</pre>
<p>other example:</p>
<pre class="prettyprint">
// in App controller
public static function index(){
    Manual::index();
}
</pre>
<br>


<h3 id="conventions">Naming conventions</h3>
<p>For any who has ever worked with an MVC framework that should be nothing new: the link between controller and view is done by correctly naming the folders and files to the controller and the calling function.</p>
<pre class="prettyprint">
// Manual controller
public static function coreconcepts(){
    // tries to render the view at 
    // views/Manual/coreconcepts.php
    self::render();
}
</pre>
<br>


<h3 id="plain">DEVELOPEMENT mode and Exceptions</h3>
<p>TODO</p>
<br>


<h3 id="plain">PLAIN_PHP namespace</h3>
<p>To not collide with any prefered Controller names, i put all the framework classes under the PLAIN_PHP namespace. If you want to create a new Controller, keep in mind to use the correct namespace!</p>
<pre class="prettyprint">
class MyController extends PLAIN_PHP\Controller { ... }

//or equivalent    
use PLAIN_PHP\Controller;
class MyController extends Controller { ... }
</pre>
<br>

<h3 id="plain">The public folder</h3>
<p>The public folder contains all non-php files that should be accassable from the outside. JavaScript, CSS or image-files. All other folders are protected with a .htaccess file.</p>
<br>


<h3 id="plain">Plain PHP</h3>
<p>The framework consists of pure PHP code, nothing magical like the popular PHP annotations or something else is used, but only the native PHP language constructs.</p>
