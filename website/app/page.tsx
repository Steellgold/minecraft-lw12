import { OnlineCount } from "@/lib/components/online-count";
import { Button } from "@/lib/components/ui/button";
import { Card, CardContent, CardDescription, CardFooter, CardHeader } from "@/lib/components/ui/card";
import { Input } from "@/lib/components/ui/input";

const Home = () => {
  return (
    <div className="flex items-center justify-center h-screen">
      <Card className="w-full max-w-sm">
        <CardHeader>
          <CardDescription>
            Get the player statistics of the laser-games server
          </CardDescription>
        </CardHeader>
        <CardContent className="grid gap-4 -mb-3">
          <form>
            <Input type="text" placeholder="Username" />
          </form>
        </CardContent>

        <CardFooter className="flex flex-col gap-4">
          <Button className="w-full">
            Search
          </Button>

        </CardFooter>

        <CardFooter className="-mt-4">
          <OnlineCount />
        </CardFooter>
      </Card>
    </div>
  );
}

export default Home;