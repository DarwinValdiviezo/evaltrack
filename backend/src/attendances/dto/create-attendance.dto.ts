import { IsNumber, IsOptional, IsString } from 'class-validator';

export class CreateAttendanceDto {
  @IsNumber()
  eventId: number;

  @IsOptional()
  @IsString()
  notes?: string;
} 