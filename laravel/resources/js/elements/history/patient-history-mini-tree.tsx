import React, { useState } from 'react';
import { ChevronDown, ChevronRight, Folder, FolderOpen, FileText, Calendar, User, Stethoscope, Bed, AlertTriangle, FlaskConical, Camera, Heart, Activity } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

interface Treatment {
  id: string;
  name: string;
  date: string;
  doctor?: string;
  status?: 'completed' | 'ongoing' | 'pending';
}

interface Department {
  id: string;
  name: string;
  icon: React.ReactNode;
  color: string;
  treatments: Treatment[];
  isExpanded?: boolean;
}

interface PatientHistoryData {
  patientName: string;
  patientId: string;
  departments: Department[];
}

interface PatientHistoryMiniTreeProps {
  patientData: PatientHistoryData;
  onTreatmentClick?: (departmentId: string, treatmentId: string) => void;
  className?: string;
}

const PatientHistoryMiniTree: React.FC<PatientHistoryMiniTreeProps> = ({
  patientData,
  onTreatmentClick,
  className
}) => {
  const [expandedDepartments, setExpandedDepartments] = useState<Record<string, boolean>>({});

  const toggleDepartment = (departmentId: string) => {
    setExpandedDepartments(prev => ({
      ...prev,
      [departmentId]: !prev[departmentId]
    }));
  };

  const handleTreatmentClick = (departmentId: string, treatmentId: string) => {
    onTreatmentClick?.(departmentId, treatmentId);
  };

  const getStatusColor = (status?: string) => {
    switch (status) {
      case 'completed':
        return 'bg-green-100 text-green-800 border-green-200';
      case 'ongoing':
        return 'bg-blue-100 text-blue-800 border-blue-200';
      case 'pending':
        return 'bg-yellow-100 text-yellow-800 border-yellow-200';
      default:
        return 'bg-gray-100 text-gray-800 border-gray-200';
    }
  };

  // Default patient data for demonstration
  const defaultPatientData: PatientHistoryData = {
    patientName: "John Doe",
    patientId: "PS/2025/10/000001",
    departments: [
      {
        id: 'emergency',
        name: 'Emergency',
        icon: <AlertTriangle className="w-4 h-4" />,
        color: 'text-red-600',
        treatments: [
          { id: 'uerco389rcnojw8qr4', name: 'Emergency Treatment #1', date: '2025-11-01', doctor: 'Dr. Smith', status: 'completed' },
          { id: '9384cn894uy9o8q3', name: 'Emergency Treatment #2', date: '2025-11-02', doctor: 'Dr. Johnson', status: 'completed' }
        ]
      },
      {
        id: 'inpatient',
        name: 'In Patient',
        icon: <Bed className="w-4 h-4" />,
        color: 'text-blue-600',
        treatments: [
          { id: 'ajsf9u48fc48nåua48', name: 'Inpatient Care #1', date: '2025-10-28', doctor: 'Dr. Wilson', status: 'ongoing' },
          { id: 'iuac48baw4bybctyb9', name: 'Inpatient Care #2', date: '2025-10-30', doctor: 'Dr. Brown', status: 'completed' }
        ]
      },
      {
        id: 'opd',
        name: 'OPD',
        icon: <Stethoscope className="w-4 h-4" />,
        color: 'text-green-600',
        treatments: [
          { id: 'aow4cjlo', name: 'OPD Consultation #1', date: '2025-10-25', doctor: 'Dr. Davis', status: 'completed' },
          { id: 'ofiaehlobhvew', name: 'OPD Follow-up #1', date: '2025-11-01', doctor: 'Dr. Davis', status: 'completed' }
        ]
      },
      {
        id: 'dental',
        name: 'Dental',
        icon: <Activity className="w-4 h-4" />,
        color: 'text-purple-600',
        treatments: [
          { id: 'dental001', name: 'Dental Checkup', date: '2025-10-20', doctor: 'Dr. White', status: 'completed' }
        ]
      },
      {
        id: 'lab',
        name: 'Lab',
        icon: <FlaskConical className="w-4 h-4" />,
        color: 'text-orange-600',
        treatments: [
          { id: 'lab001', name: 'Blood Test', date: '2025-10-26', status: 'completed' },
          { id: 'lab002', name: 'X-Ray', date: '2025-10-27', status: 'completed' }
        ]
      },
      {
        id: 'radiology',
        name: 'Radiology',
        icon: <Camera className="w-4 h-4" />,
        color: 'text-indigo-600',
        treatments: [
          { id: 'rad001', name: 'CT Scan', date: '2025-10-29', doctor: 'Dr. Lee', status: 'completed' }
        ]
      },
      {
        id: 'ultrasound',
        name: 'Ultrasound',
        icon: <Heart className="w-4 h-4" />,
        color: 'text-pink-600',
        treatments: [
          { id: 'ultra001', name: 'Abdominal Ultrasound', date: '2025-10-31', doctor: 'Dr. Taylor', status: 'completed' }
        ]
      }
    ]
  };

  const data = patientData || defaultPatientData;

  return (
    <Card className={cn("w-full max-w-md", className)}>
      <CardHeader className="pb-3">
        <CardTitle className="flex items-center space-x-2 text-lg">
          <User className="w-5 h-5" />
          <span>Patient History</span>
        </CardTitle>
        <div className="text-sm text-gray-600">
          <div className="font-medium">{data.patientName}</div>
          <div className="text-xs text-gray-500">{data.patientId}</div>
        </div>
      </CardHeader>
      
      <CardContent className="space-y-2">
        {data.departments.map((department) => {
          const isExpanded = !!expandedDepartments[department.id];
          const treatmentCount = department.treatments.length;
          
          return (
            <div key={department.id} className="space-y-1">
              {/* Department Folder */}
              <div
                className="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors select-none"
                onClick={() => toggleDepartment(department.id)}
                data-department-id={department.id}
                data-expanded={isExpanded}
              >
                <div className="flex items-center space-x-1">
                  {isExpanded ? (
                    <ChevronDown className="w-4 h-4 text-gray-500" />
                  ) : (
                    <ChevronRight className="w-4 h-4 text-gray-500" />
                  )}
                  {isExpanded ? (
                    <FolderOpen className="w-4 h-4 text-yellow-500" />
                  ) : (
                    <Folder className="w-4 h-4 text-yellow-600" />
                  )}
                </div>
                
                <div className="flex items-center space-x-2 flex-1">
                  <span className={department.color}>
                    {department.icon}
                  </span>
                  <span className="font-medium text-sm">{department.name}</span>
                  <Badge variant="secondary" className="text-xs px-2 py-0">
                    {treatmentCount}
                  </Badge>
                </div>
              </div>
              
              {/* Treatment Files */}
              {isExpanded && (
                <div className="ml-6 space-y-1">
                  {department.treatments.map((treatment) => (
                    <div
                      key={treatment.id}
                      className="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group"
                      onClick={() => handleTreatmentClick(department.id, treatment.id)}
                    >
                      <FileText className="w-3 h-3 text-gray-400" />
                      <div className="flex-1 min-w-0">
                        <div className="text-sm font-medium text-gray-700 truncate group-hover:text-gray-900">
                          {treatment.name}
                        </div>
                        <div className="text-xs text-gray-500 flex items-center space-x-2">
                          <span className="flex items-center space-x-1">
                            <Calendar className="w-3 h-3" />
                            <span>{treatment.date}</span>
                          </span>
                          {treatment.doctor && (
                            <span className="flex items-center space-x-1">
                              <User className="w-3 h-3" />
                              <span>{treatment.doctor}</span>
                            </span>
                          )}
                        </div>
                      </div>
                      {treatment.status && (
                        <div className={cn(
                          "text-xs px-2 py-1 rounded-full border",
                          getStatusColor(treatment.status)
                        )}>
                          {treatment.status}
                        </div>
                      )}
                    </div>
                  ))}
                  
                  {department.treatments.length === 0 && (
                    <div className="text-xs text-gray-400 italic ml-5 py-2">
                      No treatments found
                    </div>
                  )}
                </div>
              )}
            </div>
          );
        })}
        
        {data.departments.length === 0 && (
          <div className="text-center py-8 text-gray-400">
            <FileText className="w-8 h-8 mx-auto mb-2 opacity-50" />
            <p className="text-sm">No patient history available</p>
          </div>
        )}
      </CardContent>
    </Card>
  );
};

export default PatientHistoryMiniTree;
