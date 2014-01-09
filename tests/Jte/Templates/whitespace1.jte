markup test1 {
    hello!
}

markup test2 {
    hello1!
    hello2!
}

markup test3 {
    [[ block ]] {{ hello1! }}
    hello2!
}

logic test4 {
    replace('block')->with(block('test2'));
}

markup test4 {
    [[ block ]] {{ hello1! }}
    hello2!
}

logic test5 {
    replace('block')->with(block('test2'));
}

markup test5 {
    [[ block ]] {{
        hello1!
    }}
    hello2!
}
markup test6 {
    [[ block ]] {{
        hello1!
    }}
    hello2!
}

logic test7 {
    replace('block')->with(iterator([1, 2, 3], function ($a, $b) {
        return self();
    }));
}

markup test7 {
    [[ block ]] {{
        hello1!
    }}
    hello2!
}

logic test8 {
    replace('block')->with(iterator([1, 2, 3], function ($a, $b) {
        return self();
    }, ' '));
}

markup test8 {
    [[ block ]] {{
        hello1!
    }}
    hello2!
}
