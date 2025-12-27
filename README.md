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

## Database
- Users:
    1. Id;
    2. Username;
    3. Email;
    4. Password;
    5. Online;
    6. Created_At.
- Clubs:
    1. Id;
    2. Admin_Id;
    3. Name;
    4. Description;
    5. Members_Num;
    6. Image_Banner;
    7. Created_At.
- Clubs_Members:
    1. Id;
    2. Club_Id;
    3. User_Id;
    4. Role;
    5. Joined_At.
- Posts:
    1. Id;
    2. Club_Id;
    3. User_Id;
    4. Content;
    5. Image_Path;
    6. Likes_Num;
    7. Created_at.
- User_Likes:
    1. Id;
    2. User_Id;
    3. Post_Id.

## Deploy and Data Base Tests
In order to connect the Nuxt project with the PHP files and everything to Hostinger, which will connect to the database, it was necessary to make some modifications to the deploy file. Additionally, it was necessary to create a specific folder and file structure. Here is the list of steps:

1. Make the deploy automatically create the static pages of the Nuxt project, transforming the .vue files into .html;
2. Create the main project folder, which would contain the deploy folder, a folder for the Nuxt project, and a folder for the PHP files;
3. Update the deploy again so that it can access both folders to connect the two projects.

Several tests were performed to ensure the connection could be executed successfully, as can be seen in the initial commit history.
