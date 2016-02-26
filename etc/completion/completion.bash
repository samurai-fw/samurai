#!/bin/bash

__samurai_comp_func () {
    COMP_WORDBREAKS=${COMP_WORDBREAKS//:}
    local cur prev commands options
    cur="${COMP_WORDS[COMP_CWORD]}"
    prev="${COMP_WORDS[COMP_CWORD-1]}"
    commands=`$1 -T | awk '{print $1}'`

    #case "${prev}" in
    #esac

    if [[ "${COMP_CWORD}" == "1" ]]; then
        COMPREPLY=($(compgen -W "${commands} spec" -- $cur))
    elif [[ "${prev}" == "spec" ]]; then
        COMPREPLY=()
    else
        options=`$1 $prev -h | grep -E "^--" | awk '{print $1}' | awk -F, '{print $1}'`
        COMPREPLY=($(compgen -W "${options}" -- $cur))
    fi
}

complete -o bashdefault -o default -F __samurai_comp_func app

