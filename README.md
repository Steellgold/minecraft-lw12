
![image](https://github.com/Steellgold/minecraft-lw12/blob/stable/title.png?raw=true)

This is a project for the [Supabase Hackaton](https://supabase.com/blog/supabase-lw12-hackathon) for the 12th Launch Week.

Team: [@Gaëtan H](https://github.com/Steellgold), [@RomainSav](https://github.com/RomainSav)

The project is a Laser Game on Minecraft, we built a website with **NextJS**, and a Minecraft Bedrock server with **PocketMine-MP**

We use differents Supabase Service, such:
- **Database** for store players games and statistics
- **Realtime** to show real-time statistics of the ongoing game
- **Storage** to store heads of players



## How to join ?
Need **Minecraft Bedrock Edition**, copy and paste the IP adress `supabase.mcbe.fr` and the port (default) `19132` for connect

Or launch directly Minecraft with added server on clicking "Launch Minecraft" button on the [homepage](https://minecraft-lw12.vercel.app/)
## Environment Variables

To run this project, you will need to add the following environment variables to your .env file

| Variable                   | Description                       |
|----------------------------|-----------------------------------|
| `DATABASE_URL`              | Your database connection string |
| `DIRECT_URL`                | Direct connection string to your database (often used for migrations) |
| `SUPER_SECRET_API_MEGA_KEY` | An API key to secure the routes can edit data |
| `URL`                       | The base URL for your application |

## Tech Stack

**Web:**
  - **Front-end**: [Next.js](https://nextui.org/), [React](https://react.dev/), [Tailwind CSS](https://tailwindcss.com/), [ui/shadcn](https://ui.shadcn.com), [Lucide Icons](https://lucide.dev), [Day.js](https://day.js.org/)
  - **Back-end**: [Supabase](https://supabase.com), [Prisma](https://www.prisma.io/)
  - **Fullstack tools**: [Zod](https://zod.dev/), [TypeScript](https://www.typescriptlang.org/)

**Minecraft Server**: PocketMine-MP (Software) and PHP 8.1

## Videos

[Demo video on YT](https://www.youtube.com/watch?v=AMxJu8juYzs)

https://github.com/user-attachments/assets/882c95fd-dcdf-4ae9-9062-07d45ed2e6b3

