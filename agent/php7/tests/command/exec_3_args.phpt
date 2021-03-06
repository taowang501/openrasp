--TEST--
hook exec (with stack)
--SKIPIF--
<?php
if (PHP_VERSION_ID < 50400) die("Skipped: before 5.4 output should be passed by reference.");
$plugin = <<<EOF
plugin.register('command', params => {
    assert(params.command == 'cd')
    assert(params.stack[0].endsWith('exec'))
    assert(params.stack[1].endsWith('test_stack_1'))
    assert(params.stack[2].endsWith('test_stack_2'))
    return block
})
EOF;
include(__DIR__.'/../skipif.inc');
?>
--INI--
openrasp.root_dir=/tmp/openrasp
--FILE--
<?php
$output = array();
$return_var = null;
function test_stack_1()
{
    exec('cd', $output, $return_var);
}
function test_stack_2()
{
    test_stack_1();
}
test_stack_2();
?>
--EXPECTREGEX--
<\/script><script>location.href="http[s]?:\/\/.*?request_id=[0-9a-f]{32}"<\/script>