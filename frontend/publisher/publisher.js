/* eslint-disable */
const ftp = require("basic-ftp");
const fs = require("fs");

let client = new ftp.Client();

let local_base_dir = "./dist";
var base_dir = "";

client.ftp.verbose = false;

upload();

async function upload() {
  await client.access({
    host: "ftp.andrii.ro",
    user: "andrii_ftp@evoting.andrii.ro",
    password: "B_F~{{BY!N(i",
    secure: false,
  });
  //console.log("working");
  //client.close()
  //process.exit()
  //aici trebuie sa stergem fisierele existente
  let index_file = base_dir + "/index.html";
  let static_dir = base_dir + "/static";

  await client.remove(index_file).catch((err) => {
    console.log("EROARE LA STERGERE index_file");
  });
  await client.removeDir(static_dir, true).catch((err) => {
    console.log("EROARE LA STERGERE static");
  });
  //
  var folders_tree = get_folders_tree(local_base_dir, base_dir);
  var files_tree = get_files_tree(local_base_dir, base_dir);

  for (i = 0; i < folders_tree.length; i++) {
    var folder_name = folders_tree[i];
    await client
      .ensureDir(folder_name.DestinationFolder, true)
      .then((x) => {
        console.log("am creat folder " + folder_name.DestinationFolder);
      })
      .catch((err) => {
        console.log(folder_name);
        console.log(err);
      });
  }

  for (i = 0; i < files_tree.length; i++) {
    var file_name = files_tree[i];
    await client
      .upload(
        fs.createReadStream(file_name.FullName),
        file_name.DestinationFolder
      )
      .then((x) => {
        console.log("am uploadat fisier " + file_name.DestinationFolder);
      })
      .catch((err) => {
        console.log(file_name.DestinationFolder);
        console.log(err);
      });
  }
  console.log("GATA, uploadat cu succes!");
  client.close();
  process.exit();
}

function get_folders_tree(folder, destFolder) {
  var ret = [];
  fs.readdirSync(folder).forEach((file) => {
    var full_file_name = folder + "/" + file;
    var is_directory = fs.lstatSync(full_file_name).isDirectory();

    var f = new FileInfo();
    f.FullName = full_file_name;
    f.FileName = file;
    f.LocalFolder = folder;

    if (is_directory) {
      f.FileType = "folder";
      f.DestinationFolder = destFolder + "/" + f.FileName;
      ret.push(f);

      var response = get_folders_tree(full_file_name, destFolder + "/" + file);
      response.forEach((new_file) => {
        ret.push(new_file);
      });
    }
  });
  return ret;
}

function get_files_tree(folder, destFolder) {
  var ret = [];
  fs.readdirSync(folder).forEach((file) => {
    var full_file_name = folder + "/" + file;
    var is_directory = fs.lstatSync(full_file_name).isDirectory();

    var f = new FileInfo();
    f.FullName = full_file_name;
    f.FileName = file;
    f.LocalFolder = folder;

    if (is_directory) {
      var response = get_files_tree(full_file_name, destFolder + "/" + file);
      response.forEach((new_file) => {
        ret.push(new_file);
      });
    } else {
      f.DestinationFolder = destFolder + "/" + file;
      f.FileType = "file";
      ret.push(f);
    }
  });
  return ret;
}

function FileInfo() {
  this.FileType = "file"; // file / folder
  this.LocalFolder = "";
  this.FullName = "";
  this.FileName = "";
  this.DestinationFolder = "";
}