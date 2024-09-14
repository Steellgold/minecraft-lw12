export type Json =
  | string
  | number
  | boolean
  | null
  | { [key: string]: Json | undefined }
  | Json[]

export type Database = {
  public: {
    Tables: {
      _GameToPlayer: {
        Row: {
          A: string
          B: string
        }
        Insert: {
          A: string
          B: string
        }
        Update: {
          A?: string
          B?: string
        }
        Relationships: [
          {
            foreignKeyName: "_GameToPlayer_A_fkey"
            columns: ["A"]
            isOneToOne: false
            referencedRelation: "Game"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "_GameToPlayer_B_fkey"
            columns: ["B"]
            isOneToOne: false
            referencedRelation: "Player"
            referencedColumns: ["uuid"]
          },
        ]
      }
      _prisma_migrations: {
        Row: {
          applied_steps_count: number
          checksum: string
          finished_at: string | null
          id: string
          logs: string | null
          migration_name: string
          rolled_back_at: string | null
          started_at: string
        }
        Insert: {
          applied_steps_count?: number
          checksum: string
          finished_at?: string | null
          id: string
          logs?: string | null
          migration_name: string
          rolled_back_at?: string | null
          started_at?: string
        }
        Update: {
          applied_steps_count?: number
          checksum?: string
          finished_at?: string | null
          id?: string
          logs?: string | null
          migration_name?: string
          rolled_back_at?: string | null
          started_at?: string
        }
        Relationships: []
      }
      Game: {
        Row: {
          finishedAt: string | null
          id: string
          simpleId: number
          startedAt: string | null
          status: Database["public"]["Enums"]["GameStatus"]
        }
        Insert: {
          finishedAt?: string | null
          id: string
          simpleId?: number
          startedAt?: string | null
          status: Database["public"]["Enums"]["GameStatus"]
        }
        Update: {
          finishedAt?: string | null
          id?: string
          simpleId?: number
          startedAt?: string | null
          status?: Database["public"]["Enums"]["GameStatus"]
        }
        Relationships: []
      }
      Player: {
        Row: {
          head: string
          isOnline: boolean
          name: string
          uuid: string
        }
        Insert: {
          head: string
          isOnline?: boolean
          name: string
          uuid: string
        }
        Update: {
          head?: string
          isOnline?: boolean
          name?: string
          uuid?: string
        }
        Relationships: []
      }
      Score: {
        Row: {
          deathCount: number
          gameId: string
          id: string
          playerUuid: string
          score: number
          team: Database["public"]["Enums"]["Team"]
        }
        Insert: {
          deathCount: number
          gameId: string
          id: string
          playerUuid: string
          score: number
          team?: Database["public"]["Enums"]["Team"]
        }
        Update: {
          deathCount?: number
          gameId?: string
          id?: string
          playerUuid?: string
          score?: number
          team?: Database["public"]["Enums"]["Team"]
        }
        Relationships: [
          {
            foreignKeyName: "Score_gameId_fkey"
            columns: ["gameId"]
            isOneToOne: false
            referencedRelation: "Game"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "Score_playerUuid_fkey"
            columns: ["playerUuid"]
            isOneToOne: false
            referencedRelation: "Player"
            referencedColumns: ["uuid"]
          },
        ]
      }
    }
    Views: {
      [_ in never]: never
    }
    Functions: {
      [_ in never]: never
    }
    Enums: {
      GameStatus: "STARTED" | "FINISHED"
      Team: "RED" | "BLUE"
    }
    CompositeTypes: {
      [_ in never]: never
    }
  }
}

type PublicSchema = Database[Extract<keyof Database, "public">]

export type Tables<
  PublicTableNameOrOptions extends
    | keyof (PublicSchema["Tables"] & PublicSchema["Views"])
    | { schema: keyof Database },
  TableName extends PublicTableNameOrOptions extends { schema: keyof Database }
    ? keyof (Database[PublicTableNameOrOptions["schema"]]["Tables"] &
        Database[PublicTableNameOrOptions["schema"]]["Views"])
    : never = never,
> = PublicTableNameOrOptions extends { schema: keyof Database }
  ? (Database[PublicTableNameOrOptions["schema"]]["Tables"] &
      Database[PublicTableNameOrOptions["schema"]]["Views"])[TableName] extends {
      Row: infer R
    }
    ? R
    : never
  : PublicTableNameOrOptions extends keyof (PublicSchema["Tables"] &
        PublicSchema["Views"])
    ? (PublicSchema["Tables"] &
        PublicSchema["Views"])[PublicTableNameOrOptions] extends {
        Row: infer R
      }
      ? R
      : never
    : never

export type TablesInsert<
  PublicTableNameOrOptions extends
    | keyof PublicSchema["Tables"]
    | { schema: keyof Database },
  TableName extends PublicTableNameOrOptions extends { schema: keyof Database }
    ? keyof Database[PublicTableNameOrOptions["schema"]]["Tables"]
    : never = never,
> = PublicTableNameOrOptions extends { schema: keyof Database }
  ? Database[PublicTableNameOrOptions["schema"]]["Tables"][TableName] extends {
      Insert: infer I
    }
    ? I
    : never
  : PublicTableNameOrOptions extends keyof PublicSchema["Tables"]
    ? PublicSchema["Tables"][PublicTableNameOrOptions] extends {
        Insert: infer I
      }
      ? I
      : never
    : never

export type TablesUpdate<
  PublicTableNameOrOptions extends
    | keyof PublicSchema["Tables"]
    | { schema: keyof Database },
  TableName extends PublicTableNameOrOptions extends { schema: keyof Database }
    ? keyof Database[PublicTableNameOrOptions["schema"]]["Tables"]
    : never = never,
> = PublicTableNameOrOptions extends { schema: keyof Database }
  ? Database[PublicTableNameOrOptions["schema"]]["Tables"][TableName] extends {
      Update: infer U
    }
    ? U
    : never
  : PublicTableNameOrOptions extends keyof PublicSchema["Tables"]
    ? PublicSchema["Tables"][PublicTableNameOrOptions] extends {
        Update: infer U
      }
      ? U
      : never
    : never

export type Enums<
  PublicEnumNameOrOptions extends
    | keyof PublicSchema["Enums"]
    | { schema: keyof Database },
  EnumName extends PublicEnumNameOrOptions extends { schema: keyof Database }
    ? keyof Database[PublicEnumNameOrOptions["schema"]]["Enums"]
    : never = never,
> = PublicEnumNameOrOptions extends { schema: keyof Database }
  ? Database[PublicEnumNameOrOptions["schema"]]["Enums"][EnumName]
  : PublicEnumNameOrOptions extends keyof PublicSchema["Enums"]
    ? PublicSchema["Enums"][PublicEnumNameOrOptions]
    : never
