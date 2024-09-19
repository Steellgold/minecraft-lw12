import { ReactElement } from "react";
import { Card, CardDescription, CardFooter, CardHeader } from "./ui/card";
import { Badge } from "./ui/badge";
import Link from "next/link";
import { buttonVariants } from "./ui/button";
import { CopyButton } from "./copy-button";

export const JoinCard = (): ReactElement => {
  return (
    <Card className="w-full max-w-sm mt-4">
      <CardHeader>
        <CardDescription>
          How to play? Join the server with the button below, or connect with the IP&nbsp;
          <Badge>supabase.mcbe.fr<CopyButton text="supabase.mcbe.fr" /></Badge>&nbsp;
          on Minecraft (Bedrock Edition).
        </CardDescription>
      </CardHeader>
      <CardFooter>
        <Link
          href="minecraft:?addExternalServer=Supabase|supabase.mcbe.fr:19132" 
          className={buttonVariants({ variant: "default" })}>
          Launch Minecraft
        </Link>
      </CardFooter>
    </Card>
  )
}