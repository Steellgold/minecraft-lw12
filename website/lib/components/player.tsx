import { Component } from "../component/component";
import { Avatar, AvatarFallback, AvatarImage } from "./ui/avatar";
import { Badge } from "./ui/badge";

type PlayerInGameProps = {
  player: {
    uuid: string;
    username: string;
    headUrl: string;
    score: number;
    team: "RED" | "BLUE";
  };
};

export const PlayerInGame: Component<PlayerInGameProps> = ({ player }) => {
  return (
    <div className="flex items-center gap-2">
      <Avatar className="rounded-none">
        <AvatarImage src={player.headUrl} />
        <AvatarFallback>{player.username[0].toUpperCase()}</AvatarFallback>
      </Avatar>
      
      <div className="flex flex-col">
        <h5 className="text-lg font-semibold text-foreground flex items-center gap-1">
          {player.username}
          <Badge variant={player.team === "RED" ? "RED" : "BLUE"}>{player.team}</Badge>
        </h5>
        <p className="text-sm text-muted-foreground">
          {player.score} points
        </p>
      </div>
    </div>
  );
}