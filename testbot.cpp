#include <iostream>
#include <fstream>
#include <sys/types.h>
#include <unistd.h>
#include <cstdlib>
#include <sys/wait.h>
#include <string>
#include <ctime>
#include <cstring>
#include <cassert>
using namespace std;

template <std::size_t N>
int execvp(const char *file, const char *const (&argv)[N])
{
    assert((N > 0) && (argv[N - 1] == nullptr));

    return execvp(file, const_cast<char *const *>(argv));
}

//argv[1] - id submita + rozszerzenie
//argv[2] - id (shortcut) zadania

enum LANG {CPP, PYT, RAM, BAP};
enum LOGTYPE {FORK_ERR, LANG_ERR, ARGS_ERR, CHILD_ERR};

const std::string currentDateTime()
{
    time_t now = time(0);
    struct tm tstruct;
    char buf[80];
    tstruct = *localtime(&now);
    strftime(buf, sizeof(buf), "%Y-%m-%d %X", &tstruct);

    return buf;
}

void logsomething(int what, string rest)
{
    fstream log;
    string logpath = "testbot.log";
    log.open(logpath, ios::out | ios::app);
    log << currentDateTime() << " ";

    if (what == FORK_ERR)
        log << "Error fork!" << "-" << rest;
    else if(what==LANG_ERR)
        log << "The file is missing or unknown" << "-" << rest;
    else if (what == ARGS_ERR)
        log << "invalid arguments" << "-" << rest;
    else if (what == CHILD_ERR)
        log << "ERROR: Child has not terminated correctly" << "-" << rest;

    log << endl;
    log.close();
}

int whatlang(char* submit, int nr)
{
    string t = submit;
    if(t==".cpp")
        return CPP;
    else if(t==".py")
        return PYT;
    else if(t==".mrram")
        return RAM;
    else if(t==".bap")
        return BAP;

    logsomething(LANG_ERR, to_string(nr)+t );
    exit(1);
}

int main(int argc, char* argv[])
{
    pid_t pid;
    int status;

    if(argc!=4)
    {
        logsomething(ARGS_ERR, "");
        exit(1);
    }

    int nr = atoi(argv[1]);

    if((pid = fork()) < 0)
    {
        logsomething(FORK_ERR, "id_submit:"+to_string(nr));
        perror("Error fork!");
        exit(1);
    }

    if(pid==0) //child
    {
        int lang = whatlang(argv[2], nr);
        string file = "submits/" + (string)argv[1] + (string)argv[2];
        string compfile = "playground/" + (string)argv[1];

        switch (lang)
        {
            case CPP:
            {
                const char *comp[] = {"g++", "-std=c++11", file.c_str(), "-o", compfile.c_str(), "-O2", nullptr};
                execvp("g++",comp);
                break;
            }
            default:
                break;
        }
        return EXIT_SUCCESS;
    }else //parent
    {
        bool compilation_error = false;
        waitpid(pid,&status,0); //wait for die a child
        if (WIFEXITED(status))
        {
            if(WEXITSTATUS(status)!=0)
                compilation_error = true;
        }
        else
            logsomething(CHILD_ERR, "Status:"+status);


    }

    return EXIT_SUCCESS;
}