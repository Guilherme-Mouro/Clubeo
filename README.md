# Clubeo
---
A web platform where users will be able to create “clubs” about their favorite topics. For each topic, users will have access to a variety of tools and features specially created to suit their club’s needs.

This project will implement Nuxt & Vue knowledge acquired in this repository environment: [Projeto Integrado 3](https://github.com/Guilherme-Mouro/Projeto-Integrado-3)

Click here to acess the site: [Clubeo](https://guimou.antrob.eu)

## Main Features
- Create posts to interact with the other users in your clubs;
- Share files, documents, or media with your club;
- Interact with other users through chat messages;
- Create events inside the platform for club members;
- Use different features specially created for your club to create a more engaging environment;
- Customize your club’s appearance and settings;
- Manage member roles and permissions;
- Invite new members using shareable links.

## Deploy and Data Base Tests
In order to connect the Nuxt project with the PHP files and everything to Hostinger, which will connect to the database, it was necessary to make some modifications to the deploy file. Additionally, it was necessary to create a specific folder and file structure. Here is the list of steps:

1. Make the deploy automatically create the static pages of the Nuxt project, transforming the .vue files into .html;
2. Create the main project folder, which would contain the deploy folder, a folder for the Nuxt project, and a folder for the PHP files;
3. Update the deploy again so that it can access both folders to connect the two projects.

Several tests were performed to ensure the connection could be executed successfully, as can be seen in the initial commit history.
