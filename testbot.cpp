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

string document_root = "/var/www/hermes/public_html/";

template <std::size_t N>
int execvp(const char *file, const char *const (&argv)[N])
{
    assert((N > 0) && (argv[N - 1] == nullptr));

    return execvp(file, const_cast<char *const *>(argv));
}

inline bool exists_test(const std::string &name)
{
    return (access(name.c_str(), F_OK) != -1);
}

//argv[1] - id submita
//argv[2] - rozszerzenie
//argv[3] - id (shortcut) zadania

enum LANG
{
    CPP,
    PYT,
    RAM,
    BAP
};
enum LOGTYPE
{
    FORK_ERR,
    LANG_ERR,
    ARGS_ERR,
    CHILD_ERR,
    COPY_CONF_ERR
};

const std::string currentDateTime()
{
    time_t now = time(0);
    struct tm tstruct;
    char buf[80];
    tstruct = *localtime(&now);
    strftime(buf, sizeof(buf), "%Y-%m-%d %X", &tstruct);

    return buf;
}

void logsomething(int what, string rest="")
{
    fstream log;
    string logpath = document_root+"testbot.log";
    log.open(logpath, ios::out | ios::app);
    log << currentDateTime() << " ";

    if (what == FORK_ERR)
        log << "Error fork!"
            << "-" << rest;
    else if (what == LANG_ERR)
        log << "The file is missing or unknown"
            << "-" << rest;
    else if (what == ARGS_ERR)
        log << "invalid arguments"
            << "-" << rest;
    else if (what == CHILD_ERR)
        log << "ERROR: Child has not terminated correctly"
            << "-" << rest;
    else if (what == COPY_CONF_ERR)
        log << "The conf file hasn't been copied properly"
            << "-" << rest;

    log << endl;
    log.close();
}

int whatlang(char *submit, int nr)
{
    string t = submit;
    if (t == ".cpp")
        return CPP;
    else if (t == ".py")
        return PYT;
    else if (t == ".mrram")
        return RAM;
    else if (t == ".bap")
        return BAP;

    logsomething(LANG_ERR, to_string(nr) + t);
    exit(1);
}

int main(int argc, char *argv[])
{
    pid_t pid;
    int status;

    if (argc != 4)
    {
        logsomething(ARGS_ERR, "");
        exit(1);
    }

    int nr = atoi(argv[1]);
    string snr = (string)argv[1];
    int lang = whatlang(argv[2], nr);

    if ((pid = fork()) < 0)
    {
        logsomething(FORK_ERR, "id_submit:" + to_string(nr));
        perror("Error fork!");
        exit(1);
    }

    if (pid == 0) //child
    {
        //path to submits
        string file = document_root+"submits/" + snr + (string)argv[2];
        //path to work directory
        string compfile = document_root+"playground/" + snr;

        switch (lang)
        {
        case CPP:
        {
            const char *comp[] = {"g++", "-std=c++11", file.c_str(), "-o", compfile.c_str(), "-O2", nullptr};
            execvp("g++", comp);
            break;
        }
        default:
            break;
        }
        return EXIT_SUCCESS;
    }
    else //parent
    {
        bool compilation_error = false;
        waitpid(pid, &status, 0); //wait for die a child
        if (WIFEXITED(status))
        {
            if (WEXITSTATUS(status) != 0)
                compilation_error = true;
        }
        else
            logsomething(CHILD_ERR, "Status:" + to_string(status));

        fstream conffile, result, sio2jail_file_stream;
        string taskpath = document_root + "tasks/" + (string)argv[3];
        string confpath = taskpath + "/conf.txt";
        string resultpath = document_root + "results/" + snr + ".txt";
        string sio2jailpath = document_root + "oiejq/sio2jail";
        string OPTS, command, program, in_test, out_test, tmp, playgroundpath, out_file, sio2jail_file;
        //path to work directory
        playgroundpath = document_root + "playground/";
        //settings of sio2jail
        OPTS += " --mount-namespace off";
        OPTS += " --pid-namespace off";
        OPTS += " --uts-namespace off";
        OPTS += " --ipc-namespace off";
        OPTS += " --net-namespace off";
        OPTS += " --capability-drop off --user-namespace off";
        OPTS += " -l oiejq/sio2jail.log";
        OPTS += " -s";

        string newconfpath = playgroundpath + snr + ".conf";
        string copy_conf_command = "cp " + confpath + " " + newconfpath;
        system(copy_conf_command.c_str());

        if(!exists_test(newconfpath))
        {
            logsomething(COPY_CONF_ERR, "submit: "+snr+"  task: "+(string)argv[3]);
            exit(1);
        }

        if(!exists_test(playgroundpath+snr)
            compilation_error = true;

        conffile.open(newconfpath, ios::in);
        result.open(resultpath, ios::out);

        int n_test, memory_limit, time_limit, max_points, memory, time, points;
        conffile >> n_test;

        for (int i = 0; i < n_test; i++)
        {
            conffile >> tmp;
            result << tmp << endl;

            if (compilation_error)
            {
                result << "0" << endl; //points
                result << "-" << endl; //time
                result << "-" << endl; //memory
                result << "3" << endl; //status
            }
            else
            {
                conffile >> max_points;
                conffile >> time_limit;
                conffile >> memory_limit;
                //string OPTS1 = OPTS + "-m " + to_string(memory_limit)+"M";
                //OPTS1 += "--rtimelimit " + time_limit;

                in_test = taskpath + "/in/" + to_string(i) + ".in";
                out_test = taskpath + "/out/" + to_string(i) + ".out";
                out_file = playgroundpath + snr + ".out";
                sio2jail_file = playgroundpath + snr + ".sio2jail";
                if (lang == CPP || lang == PYT)
                {
                    program = playgroundpath + snr;
                    command = sio2jailpath + " -f 3 -o oiaug " + OPTS + " -- " + program
                              + " < " + in_test + " > " + out_file + " 3> "+ sio2jail_file;
                    system(command.c_str());
                }

                sio2jail_file_stream.open(sio2jail_file, ios::in);
                string sio_status, sio_exitcode, sio_nvm, sio_sysc, sio_time, sio_memory;
                sio2jail_file_stream >> sio_status;
                sio2jail_file_stream >> sio_exitcode;
                sio2jail_file_stream >> sio_time;
                sio2jail_file_stream >> sio_nvm;
                sio2jail_file_stream >> sio_memory;
                sio2jail_file_stream >> sio_sysc;

                sio2jail_file_stream.close();



            }
        }

        result.close();
        conffile.close();
    }

    return EXIT_SUCCESS;
}