#include <cstdlib>
#include <string>
using namespace std;

//argv[1] - id submita
//argv[2] - rozszerzenie
//argv[3] - id (shortcut) zadania

int main(int argc, char* argv[])
{
    string path = "."; //sciezka do testbota
    string command = path + "/testbot";
    command = command + " " + argv[1] + " " + argv[2] + " " + argv[3];
    system(command.c_str());

    return 0;
}