import { IsOptional, IsString, IsEnum } from 'class-validator';
import { AttendanceStatus } from '../../types/prisma.types';

export class UpdateAttendanceDto {
  @IsOptional()
  @IsEnum(AttendanceStatus)
  status?: AttendanceStatus;

  @IsOptional()
  @IsString()
  notes?: string;
} 