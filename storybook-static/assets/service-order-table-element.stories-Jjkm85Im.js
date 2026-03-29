import{c as v,a as O,j as n}from"./utils-q4EKThuO.js";import{B as f}from"./badge-DghL8o_I.js";import{n as g,l as b}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const u=x=>{const e=v.c(15),{serviceOrder:r,className:l}=x;let s;e[0]!==l?(s=O("flex items-center gap-2 min-w-0",l),e[0]=l,e[1]=s):s=e[1];let t;e[2]!==r.so_number?(t=n.jsx("p",{className:"text-xs font-mono text-muted-foreground",children:r.so_number}),e[2]=r.so_number,e[3]=t):t=e[3];let a;e[4]!==r.type?(a=n.jsx("p",{className:"text-sm font-medium truncate",children:r.type}),e[4]=r.type,e[5]=a):a=e[5];let o;e[6]!==t||e[7]!==a?(o=n.jsxs("div",{className:"min-w-0",children:[t,a]}),e[6]=t,e[7]=a,e[8]=o):o=e[8];const p=r.so_short??r.departmentKey;let c;e[9]!==p?(c=n.jsx(f,{variant:"outline",className:"text-xs shrink-0",children:p}),e[9]=p,e[10]=c):c=e[10];let m;return e[11]!==s||e[12]!==o||e[13]!==c?(m=n.jsxs("div",{className:s,children:[o,c]}),e[11]=s,e[12]=o,e[13]=c,e[14]=m):m=e[14],m};u.__docgenInfo={description:"",methods:[],displayName:"ServiceOrderTableElement"};const y={title:"Elements/ServiceOrder/ServiceOrderTableElement",component:u,tags:["autodocs"],parameters:{layout:"padded"}},i={args:{serviceOrder:b}},d={args:{serviceOrder:g}};i.parameters={...i.parameters,docs:{...i.parameters?.docs,source:{originalSource:`{
  args: {
    serviceOrder: mockServiceOrder
  }
}`,...i.parameters?.docs?.source}}};d.parameters={...d.parameters,docs:{...d.parameters?.docs,source:{originalSource:`{
  args: {
    serviceOrder: mockServiceOrderLab
  }
}`,...d.parameters?.docs?.source}}};const k=["OPD","Lab"];export{d as Lab,i as OPD,k as __namedExportsOrder,y as default};
