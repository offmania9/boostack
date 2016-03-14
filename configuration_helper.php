<h1>Apache modules</h1>
<pre>
    mod_rewrite
    mod_headers
</pre>



<h1>Apache configuration</h1>
<pre>
    &lt;Directory /var/www/html&gt;
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        allow from all
    &lt;/Directory&gt;
</pre>


<h1>PHP.ini configuration</h1>
<pre>
    short_open_tag = On
</pre>
