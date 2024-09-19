import * as React from "react"
import { cva, type VariantProps } from "class-variance-authority"

import { cn } from "@/lib/utils"

const badgeVariants = cva(
  "inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2",
  {
    variants: {
      variant: {
        default:
          "border-transparent bg-primary text-primary-foreground shadow hover:bg-primary/80",
        secondary:
          "border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80",
        destructive:
          "border-transparent bg-destructive text-destructive-foreground shadow hover:bg-destructive/80",
        outline: "text-foreground",
        
        BLUE: "border-transparent bg-[#4444FF] shadow hover:bg-[#4444FF]/80",
        YELLOW: "border-transparent bg-[#FFD744] shadow hover:bg-[#FFD744]/80 text-black",
        GREEN: "border-transparent bg-[#008444] shadow hover:bg-[#008444]/80",
        PURPLE: "border-transparent bg-[#844484] shadow hover:bg-[#844484]/80",
        ORANGE: "border-transparent bg-[#FFA544] shadow hover:bg-[#FFA544]/80",
        PINK: "border-transparent bg-[#FFC4CB] shadow hover:bg-[#FFC4CB]/80",
        WHITE: "border-transparent bg-[#FFFFFF] shadow hover:bg-[#FFFFFF]/80",
        RED: "border-transparent bg-[#FF4444] shadow hover:bg-[#FF4444]/80"
      },
    },
    defaultVariants: {
      variant: "default",
    },
  }
)

export interface BadgeProps
  extends React.HTMLAttributes<HTMLDivElement>,
    VariantProps<typeof badgeVariants> {}

function Badge({ className, variant, ...props }: BadgeProps) {
  return (
    <div className={cn(badgeVariants({ variant }), className)} {...props} />
  )
}

export { Badge, badgeVariants }
